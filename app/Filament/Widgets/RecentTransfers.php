<?php

namespace App\Filament\Widgets;

use App\Models\Transfer;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransfers extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Últimos Traslados de Equipos';
    
    protected static ?int $sort = 8;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transfer::query()
                    ->with(['deviceable', 'origin', 'destiny', 'user', 'registeredBy', 'updatedBy'])
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
                
                TextColumn::make('origin.name')
                    ->label('Origen')
                    ->searchable()
                    ->description(fn ($record) => "Pab: {$record->origin->pavilion}"),
                
                TextColumn::make('destiny.name')
                    ->label('Destino')
                    ->searchable()
                    ->description(fn ($record) => "Pab: {$record->destiny->pavilion}"),
                
                TextColumn::make('user.name')
                    ->label('Responsable')
                    ->searchable(),
                
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
            ]);
    }
}
