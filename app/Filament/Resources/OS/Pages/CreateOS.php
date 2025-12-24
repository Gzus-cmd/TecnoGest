<?php

namespace App\Filament\Resources\OS\Pages;

use App\Filament\Resources\OS\OSResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOS extends CreateRecord
{
    protected static ?string $title = 'Registrar Sistema Operativo';

    protected static string $resource = OSResource::class;

}
