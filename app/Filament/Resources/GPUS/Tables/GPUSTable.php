<?php

namespace App\Filament\Resources\GPUS\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GPUSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')
                    ->label('Marca / Modelo')
                    ->searchable()
                    ->description(fn ($record) => "🏷️ " . ($record->model ?? 'N/A')),
                TextColumn::make('memory')
                    ->label('Tipo de Memoria')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->label('Capacidad (GB)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('interface')
                    ->label('Interfaz')
                    ->searchable(),
                TextColumn::make('frequency')
                    ->label('Frecuencia (MHz)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('watts')
                    ->label('Vatios')
                    ->numeric()
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
                //
            ])
            ->recordActions([
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
                    DeleteBulkAction::make()
                        ->label('Eliminar'),
                ])->label('Acciones en Lote'),
            ])
            ->emptyStateHeading('No hay ningún registro de modelos de tarjetas gráficas');
    }
}
