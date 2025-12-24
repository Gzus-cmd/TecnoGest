<?php

namespace App\Filament\Resources\ProjectorModels\Pages;

use App\Filament\Resources\ProjectorModels\ProjectorModelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectorModel extends EditRecord
{
    protected static string $resource = ProjectorModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
