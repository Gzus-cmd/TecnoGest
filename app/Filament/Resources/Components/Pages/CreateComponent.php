<?php

namespace App\Filament\Resources\Components\Pages;

use App\Filament\Resources\Components\ComponentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComponent extends CreateRecord
{
    protected static ?string $title = 'Registrar Componente';

    protected static string $resource = ComponentResource::class;
}
