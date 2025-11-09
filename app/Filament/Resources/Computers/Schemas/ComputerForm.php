<?php

namespace App\Filament\Resources\Computers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComputerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                ->schema([
                TextInput::make('serial')
                    ->label('Codigo')
                    ->required(),
                Grid::make(2)
                ->schema([        
                        Select::make('location_id')
                            ->label('Departamento')
                            ->relationship('location', 'name')
                            ->required(),
                        Select::make('status')
                        ->label('Estado')
                            ->options([
                    'Activo' => 'Activo',
                    'Inactivo' => 'Inactivo',
                    'En Mantenimiento' => 'En mantenimiento',
                    'Desmantelado' => 'Desmantelado',
                            
                            ])
                            ->required(),
                ])
                ]),

            Section::make('Información del Sistema')
            ->schema([
                    TextInput::make('ip_address')
                    ->label('Dirección IP'),
                Select::make('os_id')
                    ->relationship('os', 'name')
                    ->label('Sistema Operativo')
                    ->required(),])

            ]);
    }
}
