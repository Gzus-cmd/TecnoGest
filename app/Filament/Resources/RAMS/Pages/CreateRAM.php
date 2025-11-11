<?php

namespace App\Filament\Resources\RAMS\Pages;

use App\Filament\Resources\RAMS\RAMResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRAM extends CreateRecord
{

    protected static string $resource = RAMResource::class;

    protected static ?string $title = 'Registrar Modelo de Memoria RAM';

}
