<?php

namespace App\Filament\Resources\Splitters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SplitterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('ports')
                    ->required()
                    ->numeric(),
                TextInput::make('frequency')
                    ->required(),
            ]);
    }
}
