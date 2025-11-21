<?php

namespace App\Filament\Resources\Maintenances\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaintenancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('deviceable_type')
                    ->label('Tipo de Dispositivo')
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
                    ->searchable(),
                TextColumn::make('deviceable.serial')
                    ->label('Serial')
                    ->searchable(),   
                TextColumn::make('type')
                    ->label('Tipo de Mantenimiento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Preventivo' => 'warning',
                        'Correctivo' => 'danger',
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'En Proceso' => 'primary',
                        'Finalizado' => 'success',
                    }),
                TextColumn::make('registeredBy.name')
                    ->label('Registrado por')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updatedBy.name')
                    ->label('Actualizado por')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin cambios'),
                TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->date()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'Preventivo' => 'Preventivo',
                        'Correctivo' => 'Correctivo',
                    ])
                    ->label('Tipo de Mantenimiento'),
                
                SelectFilter::make('status')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Proceso' => 'En Proceso',
                        'Finalizado' => 'Finalizado',
                    ])
                    ->label('Estado'),
                
                SelectFilter::make('deviceable_type')
                    ->options([
                        'App\Models\Computer' => 'Computadora',
                        'App\Models\Printer' => 'Impresora',
                        'App\Models\Projector' => 'Proyector',
                    ])
                    ->multiple()
                    ->label('Tipo de Dispositivo'),
                
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
                            return $query->where('deviceable_type', 'App\Models\Computer')
                                ->where('deviceable_id', $id);
                        } elseif ($type === 'printer') {
                            return $query->where('deviceable_type', 'App\Models\Printer')
                                ->where('deviceable_id', $id);
                        } elseif ($type === 'projector') {
                            return $query->where('deviceable_type', 'App\Models\Projector')
                                ->where('deviceable_id', $id);
                        }
                        
                        return $query;
                    }),
                    
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('registered_from')
                            ->label('Registrado desde'),
                        DatePicker::make('registered_until')
                            ->label('Registrado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['registered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['registered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['registered_from'] ?? null) {
                            $indicators[] = 'Registrado desde ' . \Carbon\Carbon::parse($data['registered_from'])->format('d/m/Y');
                        }
                        if ($data['registered_until'] ?? null) {
                            $indicators[] = 'Registrado hasta ' . \Carbon\Carbon::parse($data['registered_until'])->format('d/m/Y');
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
                    ->modalHeading('Ejecutar Mantenimiento')
                    ->modalDescription(fn ($record) => $record->requires_workshop 
                        ? '¿Está seguro de que desea ejecutar este mantenimiento? El dispositivo será trasladado automáticamente al Taller de Informática.'
                        : '¿Está seguro de que desea marcar este mantenimiento como "En Proceso"?'
                    )
                    ->modalSubmitActionLabel('Sí, ejecutar')
                    ->action(function ($record) {
                        // Simplemente actualizamos el status, el observer se encargará del resto
                        $record->status = 'En Proceso';
                        $record->save();
                        
                        $message = $record->requires_workshop 
                            ? 'El mantenimiento ha sido iniciado y el dispositivo ha sido trasladado al Taller de Informática.'
                            : 'El mantenimiento ha sido marcado como "En Proceso".';
                        
                        Notification::make()
                            ->title('Mantenimiento iniciado')
                            ->success()
                            ->body($message)
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'Pendiente'),
                
                Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Finalizar Mantenimiento')
                    ->modalDescription('¿Está seguro de que desea finalizar este mantenimiento?')
                    ->modalSubmitActionLabel('Sí, finalizar')
                    ->action(function ($record) {
                        $record->status = 'Finalizado';
                        $record->save();
                        
                        Notification::make()
                            ->title('Mantenimiento finalizado')
                            ->success()
                            ->body('El mantenimiento ha sido completado exitosamente.')
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'En Proceso'),
                
                ViewAction::make()
                    ->label('Ver'),
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar'),
                ])->label('Acciones en Lote'),
            ])

            ->emptyStateHeading('No hay ningún registro de mantenimientos');
    }
}
