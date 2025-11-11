<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos de ubicación del departamento')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Departamento')
                                    ->required()
                                    ->placeholder('Informática, Dirección'),
                                TextInput::make('pavilion')
                                    ->label('Pabellón')
                                    ->required()
                                    ->placeholder('A, B, C, D'),
                                TextInput::make('apartment')
                                    ->label('Piso')
                                    ->numeric()
                                    ->required()
                                    ->placeholder('1, 2, 3, 4'),
                            ]),
                    ]),
            ]);
    }
}
