<?php

namespace App\Filament\Resources\SpareParts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
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
                    ->label('Marca')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('part_number')
                    ->label('N° de Parte')
                    ->searchable()
                    ->toggleable(),
                    
                TextColumn::make('components_count')
                    ->label('Instancias Totales')
                    ->counts('components')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                    
                TextColumn::make('available_components_count')
                    ->label('Disponibles')
                    ->getStateUsing(function ($record) {
                        return $record->components()
                            ->where('status', 'Operativo')
                            ->whereDoesntHave('computers')
                            ->whereDoesntHave('printers')
                            ->whereDoesntHave('projectors')
                            ->count();
                    })
                    ->badge()
                    ->color('success'),
                    
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
                        'Placa Base' => 'Placa Base',
                        'Procesador' => 'Procesador',
                        'Tarjeta Gráfica' => 'Tarjeta Gráfica',
                        'Memoria RAM' => 'Memoria RAM',
                        'Almacenamiento' => 'Almacenamiento',
                        'Monitor' => 'Monitor',
                        'Teclado' => 'Teclado',
                        'Mouse' => 'Mouse',
                        'Adaptador de Red' => 'Adaptador de Red',
                        'Fuente de Poder' => 'Fuente de Poder',
                        'Gabinete' => 'Gabinete',
                        'Dispositivo de Audio' => 'Dispositivo de Audio',
                        'Estabilizador' => 'Estabilizador',
                        'Splitter' => 'Splitter',
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
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
