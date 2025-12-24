<?php

namespace App\Filament\Resources\PrinterModels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrinterModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Modelo')
                    ->description('Datos básicos del modelo de impresora')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->required()
                                    ->placeholder('HP, Canon, Brother'),
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('LaserJet Pro, imageCLASS, HL-L8360'),
                                TextInput::make('type')
                                    ->label('Tipo')
                                    ->required()
                                    ->placeholder('Laser, Inyección, Térmica'),
                            ]),
                    ]),

                Section::make('Funcionalidades')
                    ->description('Características técnicas disponibles')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Toggle::make('color')
                                    ->label('Color')
                                    ->inline(false)
                                    ->required(),
                                Toggle::make('scanner')
                                    ->label('Escáner')
                                    ->inline(false)
                                    ->required(),
                                Toggle::make('wifi')
                                    ->label('WIFI')
                                    ->inline(false)
                                    ->required(),
                                Toggle::make('ethernet')
                                    ->label('Ethernet')
                                    ->inline(false)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
