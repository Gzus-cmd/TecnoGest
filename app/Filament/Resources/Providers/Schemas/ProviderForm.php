<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del proveedor')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ruc')
                                    ->label('RUC')
                                    ->required()
                                    ->placeholder('XX-XXXXXXX-X'),
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->placeholder('Nombre de la empresa'),
                            ]),
                    ]),

                Section::make('Contacto')
                    ->description('Información de contacto del proveedor')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->placeholder('+54 XXX XXX-XXXX'),
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->placeholder('contacto@empresa.com'),
                            ]),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->required()
                            ->placeholder('Calle Principal 123, Ciudad'),
                    ]),

                Section::make('Estado')
                    ->description('Actividad del proveedor')
                    ->schema([
                        Toggle::make('status')
                            ->label('Proveedor Activo'),
                    ]),
            ]);
    }
}
