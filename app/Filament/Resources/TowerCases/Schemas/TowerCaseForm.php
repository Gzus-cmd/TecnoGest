<?php

namespace App\Filament\Resources\TowerCases\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TowerCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('format')
                    ->required(),
            ]);
    }
}
