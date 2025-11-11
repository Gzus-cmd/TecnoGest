<?php

namespace App\Filament\Resources\Motherboards\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MotherboardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos de la placa base')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('ASUS, MSI, Gigabyte'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('ROG STRIX, MPG, Aorus'),
                            ]),
                    ]),

                Section::make('Especificaciones de Compatibilidad')
                    ->description('Configuración de procesador y memoria')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('socket')
                                    ->label('Socket')
                                    ->required()
                                    ->placeholder('LGA1700, AM5, sTRX4'),
                                TextInput::make('chipset')
                                    ->label('Chipset')
                                    ->required()
                                    ->placeholder('Z790, B850, X870'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('format')
                                    ->label('Formato')
                                    ->required()
                                    ->placeholder('ATX, Micro-ATX, Mini-ITX'),
                                TextInput::make('slots_ram')
                                    ->label('Ranuras RAM')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('2, 4, 6'),
                            ]),
                    ]),

                Section::make('Conectividad y Puertos')
                    ->description('Puertos de almacenamiento y consumo')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('ports_sata')
                                    ->label('Puertos SATA')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('4, 6, 8'),
                                TextInput::make('ports_m2')
                                    ->label('Puertos M.2')
                                    ->numeric()
                                    ->placeholder('1, 2, 3, 4'),
                                TextInput::make('watts')
                                    ->label('Vatios')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('50, 80, 120'),
                            ]),
                    ]),
            ]);
    }
}
