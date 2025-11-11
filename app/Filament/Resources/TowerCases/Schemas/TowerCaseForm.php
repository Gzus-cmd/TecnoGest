<?php

namespace App\Filament\Resources\TowerCases\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TowerCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del gabinete')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('NZXT, Corsair, Lian Li'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('H510, Crystal, O11'),
                            ]),
                    ]),

                Section::make('Especificaciones del Gabinete')
                    ->description('Formato y compatibilidad')
                    ->schema([
                        TextInput::make('format')
                            ->label('Formato')
                            ->required()
                            ->placeholder('ATX, Micro-ATX, Mini-ITX, E-ATX'),
                    ]),
            ]);
    }
}
