<?php

namespace App\Filament\Resources\AudioDevices\Pages;

use App\Filament\Resources\AudioDevices\AudioDeviceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAudioDevice extends EditRecord
{
    protected static string $resource = AudioDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
