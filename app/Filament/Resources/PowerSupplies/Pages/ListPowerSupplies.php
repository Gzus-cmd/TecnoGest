<?php

namespace App\Filament\Resources\PowerSupplies\Pages;

use App\Filament\Resources\PowerSupplies\PowerSupplyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPowerSupplies extends ListRecords
{
    protected static string $resource = PowerSupplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Fuente de Poder'),
        ];
    }
}
