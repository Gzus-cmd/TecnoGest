<?php

namespace App\Filament\Widgets;

use App\Models\Component;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CriticalComponents extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Componentes Deficientes o Retirados';

    protected static ?int $sort = 9;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Component::query()
                    ->with(['componentable', 'provider'])
                    ->whereIn('status', ['Deficiente', 'Retirado'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('serial')
                    ->label('N° de Serie')
                    ->searchable(),

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
                            str_contains($state, 'SparePart') => 'Pieza de Repuesto',
                            default => $state,
                        };
                    })
                    ->searchable(),

                TextColumn::make('componentable.model')
                    ->label('Modelo')
                    ->getStateUsing(function ($record) {
                        $componentable = $record->componentable;
                        if (!$componentable) return '-';

                        if (method_exists($componentable, 'model') || isset($componentable->model)) {
                            return $componentable->model ?? '-';
                        }

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
                        'Retirado' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('provider.name')
                    ->label('Proveedor')
                    ->searchable(),

                TextColumn::make('warranty_months')
                    ->label('Garantía (meses)')
                    ->numeric(),

                TextColumn::make('input_date')
                    ->label('Entrada')
                    ->date(),
            ]);
    }
}
