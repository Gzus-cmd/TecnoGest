<?php

namespace App\Filament\Resources\SpareParts\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class SparePartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('brand')
                    ->label('Marca / Modelo')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => "🏷️ " . ($record->model ?? 'N/A')),
                    
                TextColumn::make('part_number')
                    ->label('N° de Parte')
                    ->searchable()
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo de Repuesto')
                    ->options([
                        'Cabezal de Impresión' => 'Cabezal de Impresión',
                        'Rodillo' => 'Rodillo',
                        'Fusor' => 'Fusor',
                        'Lámpara de Proyector' => 'Lámpara de Proyector',
                        'Lente' => 'Lente',
                        'Ventilador' => 'Ventilador',
                        'Otro' => 'Otro',
                    ])
                    ->multiple()
                    ->searchable(),
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
            ]);
    }
}
