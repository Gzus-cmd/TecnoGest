<?php

namespace App\Filament\Resources\PowerSupplies\Pages;

use App\Filament\Resources\PowerSupplies\PowerSupplyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPowerSupply extends EditRecord
{
    protected static string $resource = PowerSupplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
