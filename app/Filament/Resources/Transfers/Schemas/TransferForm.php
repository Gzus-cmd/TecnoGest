<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('deviceable_type')
                    ->required(),
                TextInput::make('deviceable_id')
                    ->required()
                    ->numeric(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('origin_id')
                    ->relationship('origin', 'name')
                    ->required(),
                Select::make('destiny_id')
                    ->relationship('destiny', 'name')
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
