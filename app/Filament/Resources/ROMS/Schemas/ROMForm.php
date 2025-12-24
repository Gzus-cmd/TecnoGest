<?php

namespace App\Filament\Resources\ROMS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ROMForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del almacenamiento')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Samsung, Kingston, Seagate'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('970 EVO, A2000, Barracuda'),
                            ]),
                    ]),

                Section::make('Especificaciones de Almacenamiento')
                    ->description('Características de capacidad y tipo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('type')
                                    ->label('Tipo')
                                    ->required()
                                    ->placeholder('SSD, HDD, NVMe'),
                                TextInput::make('capacity')
                                    ->label('Capacidad (GB)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('256, 512, 1024, 2048'),
                            ]),
                    ]),

                Section::make('Rendimiento y Conectividad')
                    ->description('Velocidad e interfaz de conexión')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('interface')
                                    ->label('Interfaz')
                                    ->required()
                                    ->placeholder('SATA, NVMe, M.2'),
                                TextInput::make('speed')
                                    ->label('Velocidad (MB/s)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('550, 3500, 7000'),
                                TextInput::make('watts')
                                    ->label('Vatios')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('3, 5, 8'),
                            ]),
                    ]),
            ]);
    }
}
