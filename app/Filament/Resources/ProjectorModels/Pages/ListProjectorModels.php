<?php

namespace App\Filament\Resources\ProjectorModels\Pages;

use App\Filament\Resources\ProjectorModels\ProjectorModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectorModels extends ListRecords
{
    protected static string $resource = ProjectorModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Modelo de Proyector'),
        ];
    }
}
