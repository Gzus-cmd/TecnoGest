<?php

namespace App\Filament\Resources\CPUS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CPUForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del procesador')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Intel, AMD, etc.'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('Core i9-13900KS, Ryzen 9 7950X'),
                            ]),
                        TextInput::make('socket')
                            ->label('Socket')
                            ->required()
                            ->placeholder('LGA1700, AM5, etc.'),
                    ]),

                Section::make('Especificaciones de Rendimiento')
                    ->description('Características técnicas del procesador')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('cores')
                                    ->label('Núcleos')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('8, 12, 16'),
                                TextInput::make('threads')
                                    ->label('Hilos')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('16, 24, 32'),
                                TextInput::make('watts')
                                    ->label('Vatios (TDP)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('95, 125, 253'),
                            ]),
                    ]),

                Section::make('Arquitectura y Compatibilidad')
                    ->description('Detalles técnicos avanzados')
                    ->schema([
                        TextInput::make('architecture')
                            ->label('Arquitectura')
                            ->required()
                            ->placeholder('Raptor Lake, Zen 4, etc.'),
                    ]),
            ]);
    }
}
