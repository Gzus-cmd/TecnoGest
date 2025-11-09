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

                Section::make('Información General')->schema
                ([Grid::make(2)
                ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('version')
                    ->label('Versión')
                    ->required(),

                
                    ]),]),

                Section::make('Compatibilidad')->schema
                    ([TextInput::make('architecture')
                    ->label('Arquitectura')
                    ->required(),])
                
            ]);
    }
}
