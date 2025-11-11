<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->description('Datos personales del usuario')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('dni')
                                    ->label('DNI')
                                    ->required()
                                    ->placeholder('XX.XXX.XXX'),
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->placeholder('Juan Pérez García'),
                            ]),
                    ]),

                Section::make('Información de Contacto')
                    ->description('Correo y teléfono')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->placeholder('usuario@empresa.com'),
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->placeholder('+54 XXX XXX-XXXX'),
                            ]),
                    ]),

                Section::make('Información Laboral')
                    ->description('Cargo y estado')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('position')
                                    ->label('Cargo')
                                    ->required()
                                    ->placeholder('Técnico, Administrador'),
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->inline(false)
                                    ->required(),
                            ]),
                    ]),

                Section::make('Seguridad')
                    ->description('Contraseña de acceso')
                    ->schema([
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->hiddenOn('edit')
                            ->required(),
                    ]),
            ]);
    }
}
