<?php

namespace App\Filament\Resources\Splitters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SplitterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del distribuidor')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Belkin, TP-Link, D-Link'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('F4U098, TL-LS1008, DES-1008A'),
                            ]),
                    ]),

                Section::make('Especificaciones Técnicas')
                    ->description('Puertos y rendimiento')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ports')
                                    ->label('Puertos')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('4, 8, 16, 24'),
                                TextInput::make('frequency')
                                    ->label('Frecuencia')
                                    ->required()
                                    ->placeholder('10/100, 1000 Mbps'),
                            ]),
                    ]),
            ]);
    }
}
