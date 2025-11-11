<?php

namespace App\Filament\Resources\Stabilizers\Pages;

use App\Filament\Resources\Stabilizers\StabilizerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStabilizers extends ListRecords
{
    protected static string $resource = StabilizerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Estabilizador'),
        ];
    }
}
