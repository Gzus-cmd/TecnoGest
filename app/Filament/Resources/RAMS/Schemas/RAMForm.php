<?php

namespace App\Filament\Resources\RAMS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RAMForm
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
                TextInput::make('technology')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('frequency')
                    ->required()
                    ->numeric(),
                TextInput::make('watts')
                    ->required()
                    ->numeric(),
            ]);
    }
}
