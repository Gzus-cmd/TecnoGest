<?php

namespace App\Filament\Resources\Projectors\Pages;

use App\Filament\Resources\Projectors\ProjectorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectors extends ListRecords
{
    protected static string $resource = ProjectorResource::class;

    protected static ?string $title = 'Lista de Proyectores';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Proyector'),
        ];
    }
}
