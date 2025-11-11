<?php

namespace App\Filament\Resources\Maintenances\Pages;

use App\Filament\Resources\Maintenances\MaintenanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenances extends ListRecords
{
    protected static string $resource = MaintenanceResource::class;

    protected static ?string $title = 'Registros de Mantenimientos';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Mantenimiento'),
        ];
    }
}
