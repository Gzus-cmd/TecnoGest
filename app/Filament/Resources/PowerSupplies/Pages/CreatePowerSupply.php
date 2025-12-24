<?php

namespace App\Filament\Resources\PowerSupplies\Pages;

use App\Filament\Resources\PowerSupplies\PowerSupplyResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePowerSupply extends CreateRecord
{
    protected static ?string $title = 'Registrar Fuente de Poder';

    protected static string $resource = PowerSupplyResource::class;
}
