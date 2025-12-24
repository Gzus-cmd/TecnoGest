<?php

namespace App\Filament\Resources\AudioDevices\Pages;

use App\Filament\Resources\AudioDevices\AudioDeviceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAudioDevice extends CreateRecord
{
    protected static string $resource = AudioDeviceResource::class;

    protected static ?string $title = 'Registrar Modelo de Dispositivo de Audio';
}
