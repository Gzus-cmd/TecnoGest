<?php

namespace App\Filament\Resources\GPUS\Pages;

use App\Filament\Resources\GPUS\GPUResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGPU extends CreateRecord
{
    protected static string $resource = GPUResource::class;

    protected static ?string $title = 'Registrar Modelo de Tarjeta Gráfica';
}
