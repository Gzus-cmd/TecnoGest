<?php

namespace App\Filament\Resources\Motherboards\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MotherboardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('socket')
                    ->required(),
                TextInput::make('chipset')
                    ->required(),
                TextInput::make('format')
                    ->required(),
                TextInput::make('slots_ram')
                    ->required()
                    ->numeric(),
                TextInput::make('ports_sata')
                    ->required()
                    ->numeric(),
                TextInput::make('ports_m2')
                    ->numeric(),
                TextInput::make('watts')
                    ->required()
                    ->numeric(),
            ]);
    }
}
