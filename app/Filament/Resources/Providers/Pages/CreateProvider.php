<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Filament\Resources\Providers\ProviderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProvider extends CreateRecord
{
    protected static ?string $title = 'Registrar Proveedor';

    protected static string $resource = ProviderResource::class;
}
