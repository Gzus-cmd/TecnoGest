<?php

namespace App\Filament\Resources\PowerSupplies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PowerSupplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos de la fuente de poder')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('Corsair, EVGA, Seasonic'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('RM750e, SuperNOVA 850'),
                            ]),
                    ]),

                Section::make('Especificaciones y Certificación')
                    ->description('Potencia y eficiencia')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('watts')
                                    ->label('Vatios')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('450, 550, 650, 750, 850'),
                                TextInput::make('certification')
                                    ->label('Certificación')
                                    ->placeholder('80+ Bronze, Gold, Platinum'),
                            ]),
                    ]),
            ]);
    }
}
