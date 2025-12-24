<?php

namespace App\Filament\Resources\Stabilizers\Pages;

use App\Filament\Resources\Stabilizers\StabilizerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStabilizer extends CreateRecord
{
    protected static ?string $title = 'Registrar Estabilizador';

    protected static string $resource = StabilizerResource::class;
}
