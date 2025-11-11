<?php

namespace App\Filament\Resources\Keyboards\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KeyboardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del teclado')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Logitech, Corsair, SteelSeries'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('G Pro, K95, Apex Pro'),
                            ]),
                    ]),

                Section::make('Especificaciones')
                    ->description('Conexión e idioma')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('connection')
                                    ->label('Conexión')
                                    ->required()
                                    ->placeholder('Alámbrica, Inalámbrica, USB'),
                                TextInput::make('language')
                                    ->label('Idioma')
                                    ->required()
                                    ->placeholder('Español, Inglés, Latín'),
                            ]),
                    ]),
            ]);
    }
}
