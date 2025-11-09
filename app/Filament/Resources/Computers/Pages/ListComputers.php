<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListComputers extends ListRecords
{
    protected static string $resource = ComputerResource::class;

    protected static ?string $title = 'Lista de Computadoras';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Registrar Computadora'),
        ];
    }
}
