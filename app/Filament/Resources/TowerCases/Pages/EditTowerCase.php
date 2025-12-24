<?php

namespace App\Filament\Resources\TowerCases\Pages;

use App\Filament\Resources\TowerCases\TowerCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTowerCase extends EditRecord
{
    protected static string $resource = TowerCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
