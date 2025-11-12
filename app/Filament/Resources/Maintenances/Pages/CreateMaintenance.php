<?php

namespace App\Filament\Resources\Maintenances\Pages;

use App\Filament\Resources\Maintenances\MaintenanceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMaintenance extends CreateRecord
{
    protected static ?string $title = 'Registrar Mantenimiento';

    protected static string $resource = MaintenanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['registered_by'] = Auth::user()->id;

        return $data;
    }
}
