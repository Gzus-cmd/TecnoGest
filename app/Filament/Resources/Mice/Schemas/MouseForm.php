<?php

namespace App\Filament\Resources\Mice\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('connection')
                    ->required(),
            ]);
    }
}
