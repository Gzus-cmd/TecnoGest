<?php

namespace App\Filament\Resources\AudioDevices\Pages;

use App\Filament\Resources\AudioDevices\AudioDeviceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAudioDevices extends ListRecords
{
    protected static string $resource = AudioDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Dispositivo de Audio'),
        ];
    }
}
