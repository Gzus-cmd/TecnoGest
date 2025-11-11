<?php

namespace App\Filament\Resources\OS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OSForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del sistema operativo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->placeholder('Windows, Linux, macOS'),
                                TextInput::make('version')
                                    ->label('Versión')
                                    ->required()
                                    ->placeholder('11, 10, Ubuntu 22.04'),
                            ]),
                    ]),

                Section::make('Compatibilidad')
                    ->description('Arquitectura del sistema operativo')
                    ->schema([
                        TextInput::make('architecture')
                            ->label('Arquitectura')
                            ->required()
                            ->placeholder('32-bit, 64-bit, ARM'),
                    ]),
            ]);
    }
}
