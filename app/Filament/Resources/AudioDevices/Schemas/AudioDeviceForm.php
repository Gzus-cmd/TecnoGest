<?php

namespace App\Filament\Resources\AudioDevices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AudioDeviceForm
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
                TextInput::make('speakers')
                    ->required()
                    ->numeric(),
            ]);
    }
}
