<?php

namespace App\Filament\Resources\ROMS\Pages;

use App\Filament\Resources\ROMS\ROMResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditROM extends EditRecord
{
    protected static string $resource = ROMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
