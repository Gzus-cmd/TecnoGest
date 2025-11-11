<?php

namespace App\Filament\Resources\CPUS\Pages;

use App\Filament\Resources\CPUS\CPUResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCPU extends CreateRecord
{
    protected static string $resource = CPUResource::class;

    protected static ?string $title = 'Registrar Modelo de Procesador';

}
