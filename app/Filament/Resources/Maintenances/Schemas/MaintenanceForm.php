<?php

namespace App\Filament\Resources\Maintenances\Schemas;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Mantenimiento')
                    ->description('Datos básicos del mantenimiento')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Tipo')
                                    ->options([
                                        'Preventivo' => 'Preventivo',
                                        'Correctivo' => 'Correctivo',
                                    ])
                                    ->required(),
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'Pendiente' => 'Pendiente',
                                        'En Progreso' => 'En progreso',
                                        'Finalizado' => 'Finalizado',
                                    ])
                                    ->required(),
                            ]),
                    ]),

                Section::make('Dispositivo y Técnico')
                    ->description('Información del dispositivo a mantener')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                MorphToSelect::make('deviceable')
                            ->types([
                                MorphToSelect\Type::make(Computer::class)
                                    ->label('Computadora')
                                    ->getOptionLabelFromRecordUsing(fn (Computer $record): string => "{$record->location->pavilion} - {$record->location->name} | {$record->serial}"),
                                MorphToSelect\Type::make(Printer::class)
                                    ->label('Impresora')
                                    ->getOptionLabelFromRecordUsing(fn (Printer $record): string => "{$record->location->pavilion} - {$record->location->name} | {$record->serial}"),
                                MorphToSelect\Type::make(Projector::class)
                                    ->label('Proyector')
                                    ->getOptionLabelFromRecordUsing(fn (Projector $record): string => "{$record->location->pavilion} - {$record->location->name} | {$record->serial}"),
                            ])
                            ->label('Seleccionar Dispositivo')
                            ->required()
                            ]),
                        
                    ]),

                Section::make('Descripción')
                    ->description('Detalles del trabajo realizado')
                    ->schema([
                        Textarea::make('description')
                            ->label('Descripción del Mantenimiento')
                            ->required()
                            ->placeholder('Describa el trabajo realizado...')
                            ->columnSpanFull(),
                        
                        Checkbox::make('requires_workshop')
                            ->label('Requiere Taller de Informática')
                            ->helperText('Si se marca, el dispositivo será trasladado automáticamente al área de Informática cuando el mantenimiento esté En Progreso')
                            ->default(false),
                    ]),
            ]);
    }
}
