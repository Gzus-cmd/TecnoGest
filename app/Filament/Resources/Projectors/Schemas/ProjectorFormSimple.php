<?php

namespace App\Filament\Resources\Projectors\Schemas;

use App\Models\Location;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProjectorFormSimple
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
                    ->description('Departamento y estado del proyector')
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
            ]);
    }
}
