<?php

namespace App\Filament\Resources\Keyboards\Pages;

use App\Filament\Resources\Keyboards\KeyboardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKeyboard extends CreateRecord
{

    protected static string $resource = KeyboardResource::class;

    protected static ?string $title = 'Registrar Modelo de Teclado';

}
