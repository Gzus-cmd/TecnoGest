<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\TransferResource;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Peripheral;
use App\Models\Transfer;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransferResource::class;

    protected static ?string $title = 'Registros de Traslados';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Traslado'),
            
            Action::make('device_swap')
                ->label('Intercambio de Dispositivos')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Intercambiar Ubicaciones de Dispositivos')
                ->modalDescription('Seleccione el tipo y los dispositivos a intercambiar')
                ->modalIcon('heroicon-o-arrow-path-rounded-square')
                ->form([
                    Select::make('device_type')
                        ->label('Tipo de Dispositivo')
                        ->options([
                            'Computer' => 'CPU',
                            'Printer' => 'Impresora',
                            'Projector' => 'Proyector',
                            'Peripheral' => 'Periféricos',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('device_1_id', null);
                            $set('device_2_id', null);
                        })
                        ->helperText('Ambos dispositivos deben ser del mismo tipo'),
                    
                    Select::make('device_1_id')
                        ->label('Dispositivo 1')
                        ->required()
                        ->searchable()
                        ->live()
                        ->options(function (Get $get) {
                            $type = $get('device_type');
                            if (!$type) return [];
                            
                            return $type::whereIn('status', ['Activo', 'Inactivo'])
                                ->with('location')
                                ->get()
                                ->mapWithKeys(function ($device) use ($type) {
                                    $locationName = $device->location?->name ?? 'Sin ubicación';
                                    $status = " [{$device->status}]";
                                    
                                    if ($type === 'Computer') {
                                        $label = "{$device->serial} - {$locationName}{$status}";
                                    } elseif ($type === 'Peripheral') {
                                        $label = "{$device->code} - {$locationName}{$status}";
                                    } else {
                                        $label = "{$device->serial} - {$locationName}{$status}";
                                    }
                                    
                                    return [$device->id => $label];
                                });
                        })
                        ->helperText('Solo se muestran dispositivos Activos o Inactivos'),
                    
                    \Filament\Forms\Components\Toggle::make('swap_peripherals')
                        ->label('Intercambiar Periféricos')
                        ->helperText('Intercambiar también los periféricos asignados')
                        ->default(true)
                        ->visible(fn (Get $get) => $get('device_type') === 'Computer'),
                    
                    Select::make('device_2_id')
                        ->label('Dispositivo 2')
                        ->required()
                        ->searchable()
                        ->options(function (Get $get) {
                            $type = $get('device_type');
                            $device1Id = $get('device_1_id');
                            if (!$type) return [];
                            
                            return $type::whereIn('status', ['Activo', 'Inactivo'])
                                ->where('id', '!=', $device1Id)
                                ->with('location')
                                ->get()
                                ->mapWithKeys(function ($device) use ($type) {
                                    $locationName = $device->location?->name ?? 'Sin ubicación';
                                    $status = " [{$device->status}]";
                                    
                                    if ($type === 'Computer') {
                                        $label = "{$device->serial} - {$locationName}{$status}";
                                    } elseif ($type === 'Peripheral') {
                                        $label = "{$device->code} - {$locationName}{$status}";
                                    } else {
                                        $label = "{$device->serial} - {$locationName}{$status}";
                                    }
                                    
                                    return [$device->id => $label];
                                });
                        })
                        ->helperText('Excluye automáticamente el Dispositivo 1'),
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data) {
                        $type = $data['device_type'];
                        $id1 = $data['device_1_id'];
                        $id2 = $data['device_2_id'];
                        
                        $device1 = $type::find($id1);
                        $device2 = $type::find($id2);
                        
                        $originalLocation1 = $device1->location_id;
                        $originalLocation2 = $device2->location_id;
                        $originalStatus1 = $device1->status;
                        $originalStatus2 = $device2->status;
                        
                        // Intercambiar ubicaciones
                        $device1->location_id = $originalLocation2;
                        $device2->location_id = $originalLocation1;
                        
                        // Intercambiar estados si uno es Activo y el otro Inactivo (reposición)
                        if (($originalStatus1 === 'Activo' && $originalStatus2 === 'Inactivo') ||
                            ($originalStatus1 === 'Inactivo' && $originalStatus2 === 'Activo')) {
                            $device1->status = $originalStatus2;
                            $device2->status = $originalStatus1;
                        }
                        
                        $message = 'Dispositivos intercambiados';
                        
                        // Si son Computers (CPU) Y el toggle está activado, intercambiar periféricos
                        if ($type === Computer::class && ($data['swap_peripherals'] ?? true)) {
                            $originalPeripheral1 = $device1->peripheral_id;
                            $originalPeripheral2 = $device2->peripheral_id;
                            
                            $device1->peripheral_id = $originalPeripheral2;
                            $device2->peripheral_id = $originalPeripheral1;
                            
                            // Actualizar computer_id en los peripherals si existen
                            if ($originalPeripheral1) {
                                \App\Models\Peripheral::find($originalPeripheral1)->update(['computer_id' => $id2]);
                            }
                            if ($originalPeripheral2) {
                                \App\Models\Peripheral::find($originalPeripheral2)->update(['computer_id' => $id1]);
                            }
                            
                            $message = 'CPU intercambiados (ubicación y periféricos)';
                        } elseif ($type === Computer::class) {
                            $message = 'CPU intercambiados (solo ubicación)';
                        }
                        
                        $device1->save();
                        $device2->save();

                        
                        // Crear registros de traslado para ambos dispositivos
                        Transfer::create([
                            'deviceable_type' => $type,
                            'deviceable_id' => $id1,
                            'origin_id' => $originalLocation1,
                            'destiny_id' => $originalLocation2,
                            'registered_by' => Auth::id(),
                            'status' => 'Finalizado',
                            'date' => now(),
                            'reason' => 'Intercambio de ubicación',
                        ]);
                        
                        Transfer::create([
                            'deviceable_type' => $type,
                            'deviceable_id' => $id2,
                            'origin_id' => $originalLocation2,
                            'destiny_id' => $originalLocation1,
                            'registered_by' => Auth::id(),
                            'status' => 'Finalizado',
                            'date' => now(),
                            'reason' => 'Intercambio de ubicación',
                        ]);
                        
                        Notification::make()
                            ->success()
                            ->title('Intercambio Exitoso')
                            ->body($message)
                            ->send();
                    });
                })
                ->modalSubmitActionLabel('Realizar Intercambio')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }
}
