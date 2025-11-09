<?php

namespace App\Filament\Resources\Printers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrintersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('modelo_id')
                    ->numeric()
                    ->sortable()
                    ->label('Modelo'),
                TextColumn::make('serial')
                    ->searchable()
                    ->label('Número de Serie'),
                TextColumn::make('location.name')
                    ->searchable()
                    ->label('Departamento'),
                TextColumn::make('ip_address')
                    ->searchable()
                    ->label('Dirección IP'),
                TextColumn::make('status')
                    ->badge()
                    ->label('Estado'),
                TextColumn::make('input_date')
                    ->date()
                    ->sortable()
                    ->label('Entrada'),
                TextColumn::make('output_date')
                    ->date()
                    ->sortable()
                    ->label('Salida'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Registro')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualización')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
