<?php

namespace App\Filament\Resources\CPUS\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CPUSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')
                    ->label('Marca / Modelo')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => "🏷️ " . ($record->model ?? 'N/A')),
                TextColumn::make('socket')
                    ->label('Socket')
                    ->searchable(),
                TextColumn::make('cores')
                    ->label('Núcleos')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('threads')
                    ->label('Hilos')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('watts')
                    ->label('Vatios')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('architecture')
                    ->label('Arquitectura')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->emptyStateHeading('No hay ningún registro de modelos de procesadores');
    }
}
