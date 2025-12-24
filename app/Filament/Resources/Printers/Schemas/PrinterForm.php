<?php

namespace App\Filament\Resources\Printers\Schemas;

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

class PrinterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Impresora')
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
                    ->description('Departamento e estado de la impresora')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'Activo' => 'Activo',
                                        'Inactivo' => 'Inactivo',
                                    ])
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

                Section::make('Conectividad y Garantía')
                    ->description('Configuración de red e información de garantía')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ip_address')
                                    ->label('Dirección IP')
                                    ->placeholder('192.168.1.100'),
                                TextInput::make('warranty_months')
                                    ->label('Meses de Garantía')
                                    ->numeric()
                                    ->placeholder('12, 24, 36'),
                            ]),
                    ]),

                Section::make('Fechas Importantes')
                    ->description('Registro de entrada y salida')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('input_date')
                                    ->label('Fecha de Entrada')
                                    ->required(),
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
                                        
                                        $query = Component::where('componentable_type', 'App\Models\Stabilizer')
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
