<?php

namespace App\Filament\Resources\OS\Pages;

use App\Filament\Resources\OS\OSResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOS extends ListRecords
{
    protected static string $resource = OSResource::class;

    protected static ?string $title = 'Lista de Sistemas Operativos';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Sistema Operativo'),
        ];
    }
}
