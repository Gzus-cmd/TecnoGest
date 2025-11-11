<?php

namespace App\Filament\Resources\RAMS\Pages;

use App\Filament\Resources\RAMS\RAMResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRAMS extends ListRecords
{
    protected static string $resource = RAMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Memoria RAM'),
        ];
    }
}
