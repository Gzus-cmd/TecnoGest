<?php

namespace App\Filament\Resources\ROMS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ROMForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('interface')
                    ->required(),
                TextInput::make('speed')
                    ->required()
                    ->numeric(),
                TextInput::make('watts')
                    ->required()
                    ->numeric(),
            ]);
    }
}
