<?php

namespace App\Filament\Resources\Transfers\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('deviceable_type')
                    ->label('Dispositivo')
                    ->formatStateUsing(function ($state) {
                        if (str_contains($state, 'Computer')) {
                            return 'Computadora';
                        } elseif (str_contains($state, 'Printer')) {
                            return 'Impresora';
                        } elseif (str_contains($state, 'Projector')) {
                            return 'Proyector';
                        }
                        return $state;
                    })
                    ->weight('bold')
                    ->description(fn ($record) => $record->deviceable ? '🔢 ' . $record->deviceable->serial : '')
                    ->searchable(['deviceable.serial'])
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Responsable')
                    ->searchable(),
                TextColumn::make('origin.name')
                    ->label('Origen')
                    ->searchable(),
                TextColumn::make('destiny.name')
                    ->label('Destino')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'gray',
                        'En Proceso' => 'warning',
                        'Finalizado' => 'success',
                    }),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('registeredBy.name')
                    ->label('Registrado Por')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->label('Actualizado Por')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('deviceable_type')
                    ->options([
                        'Computer' => 'Computadora',
                        'Printer' => 'Impresora',
                        'Projector' => 'Proyector',
                    ])
                    ->multiple()
                    ->label('Tipo de Dispositivo'),
                
                SelectFilter::make('status')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Proceso' => 'En Proceso',
                        'Finalizado' => 'Finalizado',
                    ])
                    ->multiple()
                    ->label('Estado'),
                
                SelectFilter::make('deviceable')
                    ->label('Dispositivo Específico')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        $results = [];
                        
                        // Buscar computadoras
                        $computers = \App\Models\Computer::where('serial', 'like', "%{$search}%")
                            ->limit(10)
                            ->get();
                        foreach ($computers as $computer) {
                            $results["computer-{$computer->id}"] = "Computadora: {$computer->serial}";
                        }
                        
                        // Buscar impresoras
                        $printers = \App\Models\Printer::where('serial', 'like', "%{$search}%")
                            ->limit(10)
                            ->get();
                        foreach ($printers as $printer) {
                            $results["printer-{$printer->id}"] = "Impresora: {$printer->serial}";
                        }
                        
                        // Buscar proyectores
                        $projectors = \App\Models\Projector::where('serial', 'like', "%{$search}%")
                            ->limit(10)
                            ->get();
                        foreach ($projectors as $projector) {
                            $results["projector-{$projector->id}"] = "Proyector: {$projector->serial}";
                        }
                        
                        return $results;
                    })
                    ->getOptionLabelUsing(function ($value) {
                        if (!$value) return null;
                        
                        [$type, $id] = explode('-', $value);
                        
                        if ($type === 'computer') {
                            $device = \App\Models\Computer::find($id);
                            return $device ? "Computadora: {$device->serial}" : null;
                        } elseif ($type === 'printer') {
                            $device = \App\Models\Printer::find($id);
                            return $device ? "Impresora: {$device->serial}" : null;
                        } elseif ($type === 'projector') {
                            $device = \App\Models\Projector::find($id);
                            return $device ? "Proyector: {$device->serial}" : null;
                        }
                        
                        return null;
                    })
                    ->query(function (Builder $query, $value) {
                        if (!$value) return $query;
                        
                        [$type, $id] = explode('-', $value);
                        
                        if ($type === 'computer') {
                            return $query->where('deviceable_type', 'Computer')
                                ->where('deviceable_id', $id);
                        } elseif ($type === 'printer') {
                            return $query->where('deviceable_type', 'Printer')
                                ->where('deviceable_id', $id);
                        } elseif ($type === 'projector') {
                            return $query->where('deviceable_type', 'Projector')
                                ->where('deviceable_id', $id);
                        }
                        
                        return $query;
                    }),
                
                SelectFilter::make('origin_id')
                    ->relationship('origin', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Ubicación Origen'),
                
                SelectFilter::make('destiny_id')
                    ->relationship('destiny', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Ubicación Destino'),
                    
                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Fecha desde'),
                        DatePicker::make('date_until')
                            ->label('Fecha hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'Desde ' . \Carbon\Carbon::parse($data['date_from'])->format('d/m/Y');
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators[] = 'Hasta ' . \Carbon\Carbon::parse($data['date_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('ejecutar')
                    ->label('Ejecutar')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Ejecutar Traslado')
                    ->modalDescription(fn ($record) => "¿Desea iniciar el traslado del dispositivo {$record->deviceable->serial}?")
                    ->modalSubmitActionLabel('Sí, ejecutar')
                    ->successNotificationTitle('Traslado en proceso')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'En Proceso',
                            'updated_by' => Auth::id(),
                        ]);
                    })
                    ->after(function () {
                        // Refrescar la tabla después de ejecutar
                    })
                    ->visible(fn ($record) => 
                        auth()->user()?->can('TransferExecute') && 
                        $record->status === 'Pendiente'
                    ),
                
                Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Finalizar Traslado')
                    ->modalDescription(fn ($record) => "¿Desea finalizar el traslado y mover el dispositivo {$record->deviceable->serial} a {$record->destiny->name}?")
                    ->modalSubmitActionLabel('Sí, finalizar')
                    ->action(function ($record) {
                        $device = $record->deviceable;
                        $oldLocation = $device->location->name ?? 'Sin ubicación';
                        $destinyName = $record->destiny->name;
                        $origin = $record->origin;
                        
                        // Verificar si el dispositivo está inactivo y sale de un taller
                        $willActivate = false;
                        if ($device->status === 'Inactivo') {
                            if ($origin && $origin->is_workshop) {
                                $willActivate = true;
                            }
                        }
                        
                        $record->update([
                            'status' => 'Finalizado',
                            'updated_by' => Auth::id(),
                        ]);
                        
                        // La actualización de ubicación se hace automáticamente en el observer
                        
                        $message = "El dispositivo ha sido movido de '{$oldLocation}' a '{$destinyName}'.";
                        if ($willActivate) {
                            $message .= " El dispositivo ha sido activado automáticamente.";
                        }
                        
                        Notification::make()
                            ->title('Traslado finalizado')
                            ->success()
                            ->body($message)
                            ->send();
                    })
                    ->visible(fn ($record) => 
                        auth()->user()?->can('TransferFinish') && 
                        $record->status === 'En Proceso'
                    ),
                
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver'),
                    EditAction::make()
                        ->label('Editar'),
                    DeleteAction::make()
                        ->label('Eliminar'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('primary')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de traslados');
    }
}
