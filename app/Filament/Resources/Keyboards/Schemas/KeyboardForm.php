<?php

namespace App\Filament\Resources\Keyboards\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KeyboardForm
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
                TextInput::make('language')
                    ->required(),
            ]);
    }
}
