<?php

namespace App\Filament\Resources\Mice\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del ratón')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Logitech, Razer, SteelSeries'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('MX Master, DeathAdder, Rival'),
                            ]),
                    ]),

                Section::make('Especificaciones')
                    ->description('Tipo de conexión')
                    ->schema([
                        TextInput::make('connection')
                            ->label('Conexión')
                            ->required()
                            ->placeholder('Alámbrica, Inalámbrica, Bluetooth'),
                    ]),
            ]);
    }
}
