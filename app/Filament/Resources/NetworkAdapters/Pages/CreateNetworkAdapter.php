<?php

namespace App\Filament\Resources\NetworkAdapters\Pages;

use App\Filament\Resources\NetworkAdapters\NetworkAdapterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNetworkAdapter extends CreateRecord
{
    protected static string $resource = NetworkAdapterResource::class;

    protected static ?string $title = 'Registrar Adaptador de Red';

}
