<?php

namespace App\Filament\Resources\Motherboards\Pages;

use App\Filament\Resources\Motherboards\MotherboardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMotherboard extends CreateRecord
{
    protected static ?string $title = 'Registrar Placa Base';

    protected static string $resource = MotherboardResource::class;
}
