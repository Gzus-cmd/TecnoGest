<?php

namespace App\Filament\Resources\Mice\Pages;

use App\Filament\Resources\Mice\MouseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMice extends ListRecords
{
    protected static string $resource = MouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Ratón'),
        ];
    }
}
