<?php

namespace App\Filament\Resources\AudioDevices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AudioDeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del dispositivo de audio')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Sony, Bose, JBL'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('WH-1000XM5, QuietComfort, Flip 6'),
                            ]),
                    ]),

                Section::make('Especificaciones Técnicas')
                    ->description('Tipo y configuración')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('type')
                                    ->label('Tipo')
                                    ->required()
                                    ->placeholder('Auriculares, Parlantes, Micrófono'),
                                TextInput::make('speakers')
                                    ->label('Parlantes')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('1, 2, 2.1, 5.1'),
                            ]),
                    ]),
            ]);
    }
}
