<?php

namespace App\Filament\Resources\GPUS\Pages;

use App\Filament\Resources\GPUS\GPUResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGPUS extends ListRecords
{
    protected static string $resource = GPUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Tarjeta Gráfica'),
        ];
    }
}
