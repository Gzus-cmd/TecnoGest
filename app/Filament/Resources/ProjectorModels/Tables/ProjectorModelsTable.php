<?php

namespace App\Filament\Resources\ProjectorModels\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectorModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable(),
                TextColumn::make('resolution')
                    ->label('Resolución')
                    ->searchable(),
                TextColumn::make('lumens')
                    ->label('Lúmenes')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('vga')
                    ->label('Puerto VGA')
                    ->boolean(),
                IconColumn::make('hdmi')
                    ->label('Puerto HDMI')
                    ->boolean(),
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
            ->emptyStateHeading('No hay ningún registro de modelos de proyectores');
    }
}
