<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('dni')
                    ->label('DNI')
                    ->required(),
                TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()->required(),
                DateTimePicker::make('email_verified_at')
                    ->label('Correo Verificado En'),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(),
            ]);
    }
}
