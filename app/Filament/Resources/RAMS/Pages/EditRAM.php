<?php

namespace App\Filament\Resources\RAMS\Pages;

use App\Filament\Resources\RAMS\RAMResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRAM extends EditRecord
{
    protected static string $resource = RAMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
