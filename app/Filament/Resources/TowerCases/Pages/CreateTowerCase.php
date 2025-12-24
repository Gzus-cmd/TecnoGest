<?php

namespace App\Filament\Resources\TowerCases\Pages;

use App\Filament\Resources\TowerCases\TowerCaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTowerCase extends CreateRecord
{
    protected static ?string $title = 'Registrar Gabinete';

    protected static string $resource = TowerCaseResource::class;
}
