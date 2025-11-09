<?php

namespace App\Filament\Resources\PowerSupplies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PowerSupplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('certification')
                    ->required(),
                TextInput::make('watts')
                    ->required()
                    ->numeric(),
            ]);
    }
}
