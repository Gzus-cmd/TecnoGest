<?php

namespace App\Filament\Resources\Components\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('componentable_type')
                    ->label('Tipo')
                    ->searchable(),
                TextColumn::make('componentable_id')
                    ->label('ID Caract.')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('serial')
                    ->label('N° de Serie')
                    ->searchable(),
                TextColumn::make('warranty_months')
                    ->label('Meses de Garantía')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('provider.name')
                    ->label('Proveedor')
                    ->searchable(),
                TextColumn::make('input_date')
                    ->label('Entrada')
                    ->date()
                    ->sortable(),
                TextColumn::make('output_date')
                    ->label('Salida')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([



            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
