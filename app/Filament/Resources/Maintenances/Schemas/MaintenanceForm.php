<?php

namespace App\Filament\Resources\Maintenances\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(['Preventiva' => 'Preventiva', 'Correctiva' => 'Correctiva'])
                    ->required(),
                TextInput::make('deviceable_type')
                    ->required(),
                TextInput::make('deviceable_id')
                    ->required()
                    ->numeric(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('status')
                    ->options(['Pendiente' => 'Pendiente', 'En Progreso' => 'En progreso', 'Finalizado' => 'Finalizado'])
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
