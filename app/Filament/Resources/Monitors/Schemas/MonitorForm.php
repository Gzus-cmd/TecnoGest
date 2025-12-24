<?php

namespace App\Filament\Resources\Monitors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MonitorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del monitor')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Dell, LG, ASUS'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('U2415, 24UP550, VP249HE'),
                            ]),
                    ]),

                Section::make('Especificaciones de Pantalla')
                    ->description('Tamaño y resolución')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('size')
                                    ->label('Tamaño (pulgadas)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('21.5, 24, 27, 32'),
                                TextInput::make('resolution')
                                    ->label('Resolución')
                                    ->required()
                                    ->placeholder('1920x1080, 2560x1440, 4K'),
                            ]),
                    ]),

                Section::make('Conectividad')
                    ->description('Puertos de conexión disponibles')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('vga')
                                    ->label('Puertos VGA')
                                    ->required(),
                                Toggle::make('hdmi')
                                    ->label('Puertos HDMI')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
