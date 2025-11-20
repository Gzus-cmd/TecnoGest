<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\TransferResource;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Peripheral;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTransfer extends CreateRecord
{
    protected static ?string $title = 'Registrar Traslado';

    protected static string $resource = TransferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['registered_by'] = Auth::user()->id;

        // Obtener el dispositivo polimórfico seleccionado
        if (isset($data['deviceable_type']) && isset($data['deviceable_id'])) {
            $deviceableType = $data['deviceable_type'];
            $deviceableId = $data['deviceable_id'];

            // Buscar el dispositivo en la clase correspondiente
            $device = $deviceableType::find($deviceableId);

            if ($device && isset($device->location_id)) {
                $data['origin_id'] = $device->location_id;
            }
        }

        return $data;
    }
}
