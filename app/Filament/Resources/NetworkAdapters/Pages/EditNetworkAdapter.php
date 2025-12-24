<?php

namespace App\Filament\Resources\NetworkAdapters\Pages;

use App\Filament\Resources\NetworkAdapters\NetworkAdapterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNetworkAdapter extends EditRecord
{
    protected static string $resource = NetworkAdapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
