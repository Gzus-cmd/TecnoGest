<?php

namespace App\Filament\Resources\Printers\Schemas;

use App\Models\Location;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PrinterFormSimple
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
                    ->description('Departamento y estado de la impresora')
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
            ]);
    }
}
