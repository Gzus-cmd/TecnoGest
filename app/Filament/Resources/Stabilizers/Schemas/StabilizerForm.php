<?php

namespace App\Filament\Resources\Stabilizers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StabilizerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('outlets')
                    ->required()
                    ->numeric(),
                TextInput::make('watts')
                    ->required()
                    ->numeric(),
            ]);
    }
}
