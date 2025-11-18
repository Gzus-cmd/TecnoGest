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
                ->modalDescription('Seleccione dos dispositivos para intercambiar. Los CPU intercambiarán ubicación y periféricos, los demás solo ubicación.')
                ->modalIcon('heroicon-o-arrow-path-rounded-square')
                ->form([
                    \Filament\Forms\Components\Toggle::make('swap_peripherals')
                        ->label('Intercambiar Periféricos')
                        ->helperText('Solo aplica cuando ambos dispositivos son CPU. Si está desactivado, solo se intercambia la ubicación.')
                        ->default(true)
                        ->live(),
                    
                    Select::make('device_1')
                        ->label('Primer Dispositivo')
                        ->required()
                        ->searchable()
                        ->live()
                        ->options(function () {
                            $devices = collect();
                            
                            $computers = Computer::with('location', 'peripheral')->get()->map(function ($d) {
                                $peripheralInfo = $d->peripheral ? ' (con periféricos)' : ' (sin periféricos)';
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                return [
                                    'type' => Computer::class,
                                    'id' => $d->id,
                                    'label' => "CPU: {$d->serial} - {$locationName}{$peripheralInfo}",
                                ];
                            });
                            
                            $printers = Printer::with('location')->get()->map(function ($d) {
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                return [
                                    'type' => Printer::class,
                                    'id' => $d->id,
                                    'label' => "Impresora: {$d->serial} - {$locationName}",
                                ];
                            });
                            
                            $projectors = Projector::with('location')->get()->map(function ($d) {
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                return [
                                    'type' => Projector::class,
                                    'id' => $d->id,
                                    'label' => "Proyector: {$d->serial} - {$locationName}",
                                ];
                            });
                            
                            $peripherals = Peripheral::with('location')->get()->map(function ($d) {
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                $computerInfo = $d->computer_id ? " (asignado)" : " (disponible)";
                                return [
                                    'type' => Peripheral::class,
                                    'id' => $d->id,
                                    'label' => "Periféricos: {$d->code} - {$locationName}{$computerInfo}",
                                ];
                            });
                            
                            $devices = $devices->merge($computers)
                                              ->merge($printers)
                                              ->merge($projectors)
                                              ->merge($peripherals);
                            
                            return $devices->mapWithKeys(function ($device) {
                                return ["{$device['type']}:{$device['id']}" => $device['label']];
                            });
                        })
                        ->helperText('Seleccione el primer dispositivo para el intercambio'),
                    
                    Select::make('device_2')
                        ->label('Segundo Dispositivo')
                        ->required()
                        ->searchable()
                        ->options(function (Get $get) {
                            $selectedDevice1 = $get('device_1');
                            $devices = collect();
                            
                            $computers = Computer::with('location', 'peripheral')->get()->map(function ($d) {
                                $peripheralInfo = $d->peripheral ? ' (con periféricos)' : ' (sin periféricos)';
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                return [
                                    'type' => Computer::class,
                                    'id' => $d->id,
                                    'label' => "CPU: {$d->serial} - {$locationName}{$peripheralInfo}",
                                ];
                            });
                            
                            $printers = Printer::with('location')->get()->map(function ($d) {
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                return [
                                    'type' => Printer::class,
                                    'id' => $d->id,
                                    'label' => "Impresora: {$d->serial} - {$locationName}",
                                ];
                            });
                            
                            $projectors = Projector::with('location')->get()->map(function ($d) {
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                return [
                                    'type' => Projector::class,
                                    'id' => $d->id,
                                    'label' => "Proyector: {$d->serial} - {$locationName}",
                                ];
                            });
                            
                            $peripherals = Peripheral::with('location')->get()->map(function ($d) {
                                $locationName = $d->location ? $d->location->name : 'Sin ubicación';
                                $computerInfo = $d->computer_id ? " (asignado)" : " (disponible)";
                                return [
                                    'type' => Peripheral::class,
                                    'id' => $d->id,
                                    'label' => "Periféricos: {$d->code} - {$locationName}{$computerInfo}",
                                ];
                            });
                            
                            $devices = $devices->merge($computers)
                                              ->merge($printers)
                                              ->merge($projectors)
                                              ->merge($peripherals);
                            
                            // Filtrar para excluir el device_1 seleccionado
                            if ($selectedDevice1) {
                                $devices = $devices->filter(function ($device) use ($selectedDevice1) {
                                    $deviceKey = "{$device['type']}:{$device['id']}";
                                    return $deviceKey !== $selectedDevice1;
                                });
                            }
                            
                            return $devices->mapWithKeys(function ($device) {
                                return ["{$device['type']}:{$device['id']}" => $device['label']];
                            });
                        })
                        ->helperText('Seleccione el segundo dispositivo para el intercambio'),
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data) {
                        [$type1, $id1] = explode(':', $data['device_1']);
                        [$type2, $id2] = explode(':', $data['device_2']);
                        
                        $device1 = $type1::find($id1);
                        $device2 = $type2::find($id2);
                        
                        $originalLocation1 = $device1->location_id;
                        $originalLocation2 = $device2->location_id;
                        
                        // Si AMBOS son Computers (CPU) Y el toggle está activado, intercambiar ubicación Y periféricos
                        if ($type1 === Computer::class && $type2 === Computer::class && ($data['swap_peripherals'] ?? true)) {
                            $originalPeripheral1 = $device1->peripheral_id;
                            $originalPeripheral2 = $device2->peripheral_id;
                            
                            // Intercambiar ubicaciones
                            $device1->location_id = $originalLocation2;
                            $device2->location_id = $originalLocation1;
                            
                            // Intercambiar periféricos
                            $device1->peripheral_id = $originalPeripheral2;
                            $device2->peripheral_id = $originalPeripheral1;
                            
                            $device1->save();
                            $device2->save();
                            
                            // Actualizar computer_id en los peripherals si existen
                            if ($originalPeripheral1) {
                                \App\Models\Peripheral::find($originalPeripheral1)->update(['computer_id' => $id2]);
                            }
                            if ($originalPeripheral2) {
                                \App\Models\Peripheral::find($originalPeripheral2)->update(['computer_id' => $id1]);
                            }
                            
                            $message = 'CPU intercambiados (ubicación y periféricos)';
                        } else {
                            // Para otros dispositivos O si el toggle está desactivado, solo intercambiar ubicación
                            $device1->location_id = $originalLocation2;
                            $device2->location_id = $originalLocation1;
                            
                            $device1->save();
                            $device2->save();
                            
                            if ($type1 === Computer::class && $type2 === Computer::class) {
                                $message = 'CPU intercambiados (solo ubicación)';
                            } else {
                                $message = 'Dispositivos intercambiados (solo ubicación)';
                            }
                        }

                        
                        // Crear registros de traslado para ambos dispositivos
                        Transfer::create([
                            'deviceable_type' => $type1,
                            'deviceable_id' => $id1,
                            'origin_id' => $originalLocation1,
                            'destiny_id' => $originalLocation2,
                            'registered_by' => Auth::id(),
                            'status' => 'Finalizado',
                            'date' => now(),
                            'reason' => 'Intercambio de ubicación',
                        ]);
                        
                        Transfer::create([
                            'deviceable_type' => $type2,
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
