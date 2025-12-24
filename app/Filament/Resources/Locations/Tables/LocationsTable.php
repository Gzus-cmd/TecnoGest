<?php

namespace App\Filament\Resources\Locations\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Departamento')
                    ->searchable(),
                TextColumn::make('pavilion')
                    ->label('Pabellón')
                    ->searchable(),
                TextColumn::make('apartment')
                    ->label('Piso'),
                IconColumn::make('is_workshop')
                    ->label('Es Taller')
                    ->boolean()
                    ->trueIcon('heroicon-o-wrench-screwdriver')
                    ->falseIcon('heroicon-o-building-office')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->tooltip(fn ($record) => $record->is_workshop 
                        ? 'Taller/Área de Informática' 
                        : 'Ubicación Regular'),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
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
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de ubicaciones');
    }
}
