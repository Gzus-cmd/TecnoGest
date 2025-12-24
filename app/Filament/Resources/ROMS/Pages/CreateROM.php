<?php

namespace App\Filament\Resources\ROMS\Pages;

use App\Filament\Resources\ROMS\ROMResource;
use Filament\Resources\Pages\CreateRecord;

class CreateROM extends CreateRecord
{
    protected static ?string $title = 'Registrar Almacenamiento';

    protected static string $resource = ROMResource::class;
}
