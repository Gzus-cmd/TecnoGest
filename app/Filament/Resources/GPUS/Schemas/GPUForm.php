<?php

namespace App\Filament\Resources\GPUS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GPUForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('memory')
                    ->required()
                    ->numeric(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('interface')
                    ->required(),
                TextInput::make('frequency')
                    ->required()
                    ->numeric(),
                TextInput::make('watts')
                    ->required()
                    ->numeric(),
            ]);
    }
}
