<?php

namespace App\Filament\Resources\Computers\Schemas;

use App\Models\Location;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ComputerFormSimple
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos de la computadora')
                    ->schema([
                        TextInput::make('serial')
                            ->label('Código/Serial')
                            ->required()
                            ->placeholder('COMP-001')
                            ->unique(ignoreRecord: true),
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

                Section::make('Información del Sistema')
                    ->description('Configuración de red y sistema operativo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ip_address')
                                    ->label('Dirección IP')
                                    ->placeholder('192.168.1.100'),
                                Select::make('os_id')
                                    ->label('Sistema Operativo')
                                    ->relationship('os', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ]),

                Section::make('Asignación de Periféricos')
                    ->description('Asigne un conjunto de periféricos (opcional)')
                    ->schema([
                        Select::make('peripheral_id')
                            ->label('Conjunto de Periféricos')
                            ->options(function ($record) {
                                $query = \App\Models\Peripheral::query();
                                
                                // Si estamos editando, incluir el periférico actual O disponibles
                                if ($record && $record->peripheral_id) {
                                    $query->where(function ($q) use ($record) {
                                        $q->whereNull('computer_id')
                                          ->orWhere('id', $record->peripheral_id);
                                    });
                                } else {
                                    // Si estamos creando, solo disponibles
                                    $query->whereNull('computer_id');
                                }
                                
                                return $query->with('location')->get()->mapWithKeys(function ($peripheral) use ($record) {
                                    $location = $peripheral->location ? " - {$peripheral->location->name}" : '';
                                    return [$peripheral->id => "{$peripheral->code}{$location}"];
                                });
                            })
                            ->searchable()
                            ->nullable()
                            ->placeholder('Sin asignar')
                            ->helperText('Seleccione un conjunto de periféricos disponible'),
                    ]),
            ]);
    }
}
