<?php

namespace App\Filament\Resources\Users\Schemas;

use Dom\Text;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Información Personal')
                    ->schema([
                        TextInput::make('dni')
                            ->label('DNI')
                            ->required(),
                            
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required(),
                    ]),

                Section::make('Información de Contacto')
                    ->schema([
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()->required(),
                    ]),

                Section::make('Información Adicional')
                    ->columns(2)
                    ->schema([
                        TextInput::make('position')
                            ->label('Cargo')
                            ->required(),
                        
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->inline(false)
                            ->required(),
                    ]),

                Section::make('Seguridad')
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
