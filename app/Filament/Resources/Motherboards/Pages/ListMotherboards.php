<?php

namespace App\Filament\Resources\Motherboards\Pages;

use App\Filament\Resources\Motherboards\MotherboardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMotherboards extends ListRecords
{
    protected static string $resource = MotherboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Placa Base'),
        ];
    }
}
