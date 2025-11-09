<?php

namespace App\Filament\Resources\Motherboards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MotherboardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('socket')
                    ->searchable(),
                TextColumn::make('chipset')
                    ->searchable(),
                TextColumn::make('format')
                    ->searchable(),
                TextColumn::make('slots_ram')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ports_sata')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ports_m2')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('watts')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
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
