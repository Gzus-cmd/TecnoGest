<?php

namespace App\Filament\Resources\GPUS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GPUForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos de la tarjeta gráfica')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('NVIDIA, AMD, Intel'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('RTX 4090, RX 7900 XTX'),
                            ]),
                    ]),

                Section::make('Especificaciones de Memoria')
                    ->description('Características de memoria de la GPU')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('memory')
                                    ->label('Tipo de Memoria')
                                    ->required()
                                    ->placeholder('GDDR6, GDDR6X, HBM2'),
                                TextInput::make('capacity')
                                    ->label('Capacidad (GB)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('4, 8, 12, 24'),
                            ]),
                    ]),

                Section::make('Rendimiento y Conectividad')
                    ->description('Especificaciones técnicas de rendimiento')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('interface')
                                    ->label('Interfaz')
                                    ->required()
                                    ->placeholder('PCIe 4.0, PCIe 3.0'),
                                TextInput::make('frequency')
                                    ->label('Frecuencia (MHz)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('2000, 2500'),
                                TextInput::make('watts')
                                    ->label('Vatios (TDP)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('320, 450, 575'),
                            ]),
                    ]),
            ]);
    }
}
