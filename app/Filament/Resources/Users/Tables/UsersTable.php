<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dni')
                    ->searchable()
                    ->label('DNI'),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nombre Completo'),
                TextColumn::make('email')
                    ->searchable()
                    ->label('Correo Electrónico'),
                TextColumn::make('phone')
                    ->searchable()
                    ->label('Teléfono'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Operativo'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Creado En')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Actualizado En')
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
                    DeleteBulkAction::make()
                    ->label('Eliminar Seleccionados'),
                ])->label('Acciones en Lote'),
            ])
            ->emptyStateHeading('No hay ningún registro de usuarios');
    }
}
