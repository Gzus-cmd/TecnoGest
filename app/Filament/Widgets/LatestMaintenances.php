<?php

namespace App\Filament\Widgets;

use App\Models\Maintenance;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestMaintenances extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Mantenimientos Recientes';
    
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Maintenance::query()
                    ->with(['deviceable', 'registeredBy', 'updatedBy'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('deviceable_type')
                    ->label('Tipo')
                    ->formatStateUsing(function ($state) {
                        return match (true) {
                            str_contains($state, 'Computer') => 'Computadora',
                            str_contains($state, 'Printer') => 'Impresora',
                            str_contains($state, 'Projector') => 'Proyector',
                            default => $state,
                        };
                    })
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        str_contains($state, 'Computer') => 'primary',
                        str_contains($state, 'Printer') => 'success',
                        str_contains($state, 'Projector') => 'warning',
                        default => 'gray',
                    }),
                
                TextColumn::make('deviceable.serial')
                    ->label('Serial')
                    ->searchable(),
                
                TextColumn::make('type')
                    ->label('Tipo de Mantenimiento')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Preventivo' => 'Preventivo',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Preventivo' => 'success',
                        'Correctivo' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'En Proceso' => 'primary',
                        'Finalizado' => 'success',
                    }),
                
                TextColumn::make('registeredBy.name')
                    ->label('Técnico')
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
            ]);
    }
}
