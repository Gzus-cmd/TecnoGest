<?php

namespace App\Filament\Resources\RAMS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RAMForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del módulo de memoria')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Kingston, Corsair, G.Skill'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('HyperX Fury, Vengeance RGB'),
                            ]),
                    ]),

                Section::make('Especificaciones de Memoria')
                    ->description('Características técnicas de la RAM')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('type')
                                    ->label('Tipo')
                                    ->required()
                                    ->placeholder('DDR4, DDR5'),
                                TextInput::make('technology')
                                    ->label('Tecnología')
                                    ->required()
                                    ->placeholder('EXPO, XMP, DOCP, etc.'),
                            ]),
                    ]),

                Section::make('Rendimiento y Consumo')
                    ->description('Velocidad y potencia de la memoria')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('capacity')
                                    ->label('Capacidad (GB)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('4, 8, 16, 32'),
                                TextInput::make('frequency')
                                    ->label('Frecuencia (MHz)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('3200, 3600, 4800'),
                                TextInput::make('watts')
                                    ->label('Vatios')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('1.2, 1.5, 1.8'),
                            ]),
                    ]),
            ]);
    }
}
