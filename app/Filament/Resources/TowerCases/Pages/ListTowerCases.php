<?php

namespace App\Filament\Resources\TowerCases\Pages;

use App\Filament\Resources\TowerCases\TowerCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTowerCases extends ListRecords
{
    protected static string $resource = TowerCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Gabinete'),
        ];
    }
}
