<?php

namespace App\Filament\Resources\ROMS\Pages;

use App\Filament\Resources\ROMS\ROMResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListROMS extends ListRecords
{
    protected static string $resource = ROMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Almacenamiento'),
        ];
    }
}
