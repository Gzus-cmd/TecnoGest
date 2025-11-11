<?php

namespace App\Filament\Resources\Stabilizers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StabilizerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del estabilizador')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('APC, Tripp Lite, Cyber Power'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('BE650M2-LM, PDUMH20-L, PR1500ELCD'),
                            ]),
                    ]),

                Section::make('Especificaciones Técnicas')
                    ->description('Tomas y potencia')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('outlets')
                                    ->label('Tomas')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('4, 6, 8, 12'),
                                TextInput::make('watts')
                                    ->label('Vatios')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('600, 900, 1200, 1500'),
                            ]),
                    ]),
            ]);
    }
}
