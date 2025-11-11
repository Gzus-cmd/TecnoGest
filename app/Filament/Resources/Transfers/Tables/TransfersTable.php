<?php

namespace App\Filament\Resources\Transfers\Tables;

use Filament\Actions\Action;
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

class TransfersTable
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
                TextColumn::make('user.name')
                    ->label('Responsable')
                    ->searchable(),
                TextColumn::make('origin.name')
                    ->label('Origen')
                    ->searchable(),
                TextColumn::make('destiny.name')
                    ->label('Destino')
                    ->searchable(),
                IconColumn::make('completado')
                    ->label('Estado')
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        $device = $record->deviceable;
                        return $device && $device->location_id === $record->destiny_id;
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->deviceable && $record->deviceable->location_id === $record->destiny_id 
                        ? 'Dispositivo en ubicación destino' 
                        : 'Dispositivo NO está en ubicación destino'),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('deviceable_type')
                    ->options([
                        'App\Models\Computer' => 'Computadora',
                        'App\Models\Printer' => 'Impresora',
                        'App\Models\Projector' => 'Proyector',
                    ])
                    ->multiple()
                    ->label('Tipo de Dispositivo'),
                
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
                Action::make('ejecutar_traslado')
                    ->label('Ejecutar Traslado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Ejecutar Traslado')
                    ->modalDescription(fn ($record) => "¿Desea mover el dispositivo {$record->deviceable->serial} a {$record->destiny->name}?")
                    ->modalSubmitActionLabel('Sí, ejecutar')
                    ->action(function ($record) {
                        $device = $record->deviceable;
                        $oldLocation = $device->location->name ?? 'Sin ubicación';
                        
                        $device->update(['location_id' => $record->destiny_id]);
                        
                        Notification::make()
                            ->title('Traslado ejecutado')
                            ->success()
                            ->body("El dispositivo ha sido movido de '{$oldLocation}' a '{$record->destiny->name}'.")
                            ->send();
                    })
                    ->visible(function ($record) {
                        $device = $record->deviceable;
                        return $device && $device->location_id !== $record->destiny_id;
                    }),
                
                ViewAction::make()
                    ->label('Ver'),
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de traslados');
    }
}
