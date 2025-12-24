<?php

namespace App\Filament\Resources\PrinterModels\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrinterModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')
                    ->label('Marca / Modelo')
                    ->searchable()
                    ->description(fn ($record) => "🏷️ " . ($record->model ?? 'N/A')),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable(),
                IconColumn::make('color')
                    ->label('Color')
                    ->boolean(),
                IconColumn::make('scanner')
                    ->label('Escáner')
                    ->boolean(),
                IconColumn::make('wifi')
                    ->label('WIFI')
                    ->boolean(),
                IconColumn::make('ethernet')
                    ->label('Ethernet')
                    ->boolean(),
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
            ->emptyStateHeading('No hay ningún registro de modelos de impresoras');
    }
}
