<?php

namespace App\Filament\Resources\Mice\Pages;

use App\Filament\Resources\Mice\MouseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMouse extends CreateRecord
{
    protected static ?string $title = 'Registrar Ratón';

    protected static string $resource = MouseResource::class;
}
