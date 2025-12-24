<?php

namespace App\Filament\Resources\Peripherals\Pages;

use App\Filament\Resources\PeripheralResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeripherals extends ListRecords
{
    protected static string $resource = PeripheralResource::class;

    protected static ?string $title = 'Periféricos';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Periférico'),
        ];
    }
}
