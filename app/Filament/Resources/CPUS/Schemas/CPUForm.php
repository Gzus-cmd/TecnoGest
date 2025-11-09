<?php

namespace App\Filament\Resources\CPUS\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CPUForm
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
                TextInput::make('cores')
                    ->required()
                    ->numeric(),
                TextInput::make('threads')
                    ->required()
                    ->numeric(),
                TextInput::make('architecture')
                    ->required(),
                TextInput::make('watts')
                    ->required()
                    ->numeric(),
            ]);
    }
}
