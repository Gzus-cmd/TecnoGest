<?php

namespace App\Filament\Resources\CPUS\Pages;

use App\Filament\Resources\CPUS\CPUResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCPUS extends ListRecords
{
    protected static string $resource = CPUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Procesador'),
        ];
    }
}
