<?php

namespace App\Filament\Resources\ProjectorModels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectorModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Modelo')
                    ->description('Datos básicos del modelo de proyector')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->required()
                                    ->placeholder('Proyector XYZ-1000'),
                                TextInput::make('resolution')
                                    ->label('Resolución')
                                    ->required()
                                    ->placeholder('1920x1200, 4K'),
                            ]),
                    ]),

                Section::make('Especificaciones Técnicas')
                    ->description('Brillo y conectividad')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('lumens')
                                    ->label('Lúmenes')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('3000, 5000, 8000'),
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->placeholder('Epson, Sony, Panasonic'),
                            ]),
                    ]),

                Section::make('Conectividad')
                    ->description('Puertos disponibles')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('vga')
                                    ->label('Puerto VGA')
                                    ->required(),
                                Toggle::make('hdmi')
                                    ->label('Puerto HDMI')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
