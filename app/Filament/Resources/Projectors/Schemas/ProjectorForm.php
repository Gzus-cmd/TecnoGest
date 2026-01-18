<?php

namespace App\Filament\Resources\Projectors\Schemas;

use App\Models\Component;
use App\Models\Location;
use App\Models\Stabilizer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ProjectorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Proyector')
                    ->description('Datos básicos y modelo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('modelo_id')
                                    ->relationship('modelo', 'model')
                                    ->label('Modelo')
                                    ->required(),
                                TextInput::make('serial')
                                    ->label('Número de Serie')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),
                    ]),

                Section::make('Ubicación y Estado')
                    ->description('Departamento e estado del proyector')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'Activo' => 'Activo',
                                        'Inactivo' => 'Inactivo',
                                    ])
                                    ->default('Activo')
                                    ->required()
                                    ->reactive(),
                                Select::make('location_id')
                                    ->label('Departamento')
                                    ->options(function (Get $get) {
                                        $status = $get('status');
                                        
                                        $query = Location::query();
                                        
                                        // Si está Inactivo, solo mostrar talleres de informática
                                        if ($status === 'Inactivo') {
                                            $query->where('is_workshop', true);
                                        }
                                        
                                        return $query->get()
                                            ->mapWithKeys(fn ($location) => [$location->id => "{$location->pavilion} | {$location->name}"]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText(fn (Get $get) => $get('status') === 'Inactivo' 
                                        ? 'Dispositivos inactivos solo pueden ir a talleres de informática' 
                                        : 'Puede seleccionar cualquier ubicación'),
                            ]),
                    ]),

                Section::make('Garantía y Fechas')
                    ->description('Información de garantía e instalación')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('warranty_months')
                                    ->label('Meses de Garantía')
                                    ->numeric()
                                    ->placeholder('12, 24, 36'),
                                DatePicker::make('input_date')
                                    ->label('Fecha de Entrada'),
                                DatePicker::make('output_date')
                                    ->label('Fecha de Salida'),
                            ]),
                    ]),

                Section::make('Componentes Adicionales')
                    ->description('Estabilizador y otros componentes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('stabilizer_component_id')
                                    ->label('Estabilizador')
                                    ->options(function ($livewire) {
                                        $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                            ? $livewire->getRecord() 
                                            : null;
                                        
                                        $query = Component::where('componentable_type', 'Stabilizer')
                                            ->where('status', 'Operativo');
                                        
                                        // Los estabilizadores pueden estar asignados a múltiples dispositivos
                                        // No aplicamos whereDoesntHave para permitir reutilización
                                        
                                        return $query->get()
                                            ->mapWithKeys(function ($component) {
                                                $stab = $component->componentable;
                                                return [$component->id => "{$stab->brand} {$stab->model} - {$stab->capacity}VA - Serial: {$component->serial}"];
                                            });
                                    })
                                    ->searchable(),
                            ]),
                    ]),
            ]);
    }
}
