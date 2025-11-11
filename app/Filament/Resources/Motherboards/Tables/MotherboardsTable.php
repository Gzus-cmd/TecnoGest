<?php

namespace App\Filament\Resources\Motherboards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MotherboardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),
                TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable(),
                TextColumn::make('socket')
                    ->label('Socket')
                    ->searchable(),
                TextColumn::make('chipset')
                    ->label('Chipset')
                    ->searchable(),
                TextColumn::make('format')
                    ->label('Formato')
                    ->searchable(),
                TextColumn::make('slots_ram')
                    ->label('Ranuras RAM')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ports_sata')
                    ->label('Puertos SATA')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ports_m2')
                    ->label('Puertos M.2')
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
            ->emptyStateHeading('No hay ningún registro de modelos de placas base');
    }
}
