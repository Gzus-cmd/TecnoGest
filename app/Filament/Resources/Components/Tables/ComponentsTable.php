<?php

namespace App\Filament\Resources\Components\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial')
                ->label('N° de Serie')
                ->searchable()
                ->sortable(),
                TextColumn::make('componentable_type')
                ->label('Tipo')
                ->formatStateUsing(function ($state) {
                    return match (true) {
                        str_contains($state, 'CPU') => 'Procesador',
                        str_contains($state, 'GPU') => 'Tarjeta Gráfica',
                        str_contains($state, 'RAM') => 'Memoria RAM',
                        str_contains($state, 'ROM') => 'Almacenamiento',
                        str_contains($state, 'PowerSupply') => 'Fuente de Poder',
                        str_contains($state, 'NetworkAdapter') => 'Adaptador de Red',
                        str_contains($state, 'Motherboard') => 'Placa Base',
                        str_contains($state, 'Monitor') => 'Monitor',
                        str_contains($state, 'Keyboard') => 'Teclado',
                        str_contains($state, 'Mouse') => 'Ratón',
                        str_contains($state, 'Stabilizer') => 'Estabilizador',
                        str_contains($state, 'TowerCase') => 'Gabinete',
                        str_contains($state, 'Splitter') => 'Splitter',
                        str_contains($state, 'AudioDevice') => 'Dispositivo de Audio',
                        default => $state,
                    };
                })
                ->searchable(),
                TextColumn::make('componentable.model')
                    ->label('Modelo')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        $componentable = $record->componentable;
                        if (!$componentable) return '-';
                        
                        // Diferentes modelos tienen diferentes campos para mostrar
                        if (method_exists($componentable, 'model') || isset($componentable->model)) {
                            return $componentable->model ?? '-';
                        }
                        
                        // Para algunos componentes, usar brand + model
                        if (isset($componentable->brand) && isset($componentable->model)) {
                            return "{$componentable->brand} {$componentable->model}";
                        }
                        
                        return '-';
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Deficiente' => 'warning',
                        'Operativo' => 'success',
                        'Retirado' => 'danger',
                    }),
                TextColumn::make('current_assignment')
                    ->label('Asignado a')
                    ->getStateUsing(function ($record) {
                        // Buscar asignación vigente
                        $computer = $record->computers->first();
                        if ($computer) {
                            return "PC: {$computer->serial} ({$computer->location->name})";
                        }
                        
                        $printer = $record->printers->first();
                        if ($printer) {
                            return "Impresora: {$printer->serial} ({$printer->location->name})";
                        }
                        
                        $projector = $record->projectors->first();
                        if ($projector) {
                            return "Proyector: {$projector->serial} ({$projector->location->name})";
                        }
                        
                        return 'Disponible';
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Disponible' ? 'success' : 'info')
                    ->searchable(false),
                TextColumn::make('provider.name')
                    ->label('Proveedor')
                    ->searchable(),
                TextColumn::make('warranty_months')
                    ->label('Garantía (meses)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('input_date')
                    ->label('Entrada')
                    ->date()
                    ->sortable(),
                TextColumn::make('output_date')
                    ->label('Salida')
                    ->date()
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
                SelectFilter::make('componentable_type')
                    ->options([
                        'App\Models\CPU' => 'Procesador',
                        'App\Models\GPU' => 'Tarjeta Gráfica',
                        'App\Models\RAM' => 'Memoria RAM',
                        'App\Models\ROM' => 'Almacenamiento',
                        'App\Models\PowerSupply' => 'Fuente de Poder',
                        'App\Models\NetworkAdapter' => 'Adaptador de Red',
                        'App\Models\Motherboard' => 'Placa Base',
                        'App\Models\Monitor' => 'Monitor',
                        'App\Models\Keyboard' => 'Teclado',
                        'App\Models\Mouse' => 'Ratón',
                        'App\Models\Stabilizer' => 'Estabilizador',
                        'App\Models\TowerCase' => 'Gabinete',
                        'App\Models\Splitter' => 'Splitter',
                        'App\Models\AudioDevice' => 'Dispositivo de Audio',
                    ])
                    ->multiple()
                    ->label('Tipo de Componente'),
                
                SelectFilter::make('status')
                    ->options([
                        'Operativo' => 'Operativo',
                        'Deficiente' => 'Deficiente',
                        'Retirado' => 'Retirado',
                    ])
                    ->label('Estado'),
                
                SelectFilter::make('provider_id')
                    ->relationship('provider', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Proveedor'),
            ], layout: FiltersLayout::AboveContent)
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
                        ->label('Eliminar'),
                ])->label('Acciones en Lote'),
            ])
            ->emptyStateHeading('No hay ningún registro de componentes');
    }
}
