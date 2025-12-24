<?php

namespace App\Filament\Resources\Mice\Pages;

use App\Filament\Resources\Mice\MouseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMouse extends EditRecord
{
    protected static string $resource = MouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
