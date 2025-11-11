<?php

namespace App\Filament\Resources\NetworkAdapters\Pages;

use App\Filament\Resources\NetworkAdapters\NetworkAdapterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNetworkAdapters extends ListRecords
{
    protected static string $resource = NetworkAdapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Adaptador de Red'),
        ];
    }
}
