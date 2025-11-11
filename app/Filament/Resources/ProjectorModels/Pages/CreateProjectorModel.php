<?php

namespace App\Filament\Resources\ProjectorModels\Pages;

use App\Filament\Resources\ProjectorModels\ProjectorModelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectorModel extends CreateRecord
{
    protected static ?string $title = 'Registrar Modelo de Proyector';

    protected static string $resource = ProjectorModelResource::class;
}
