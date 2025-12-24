<?php

namespace App\Filament\Resources\Keyboards\Pages;

use App\Filament\Resources\Keyboards\KeyboardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKeyboards extends ListRecords
{
    protected static string $resource = KeyboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Teclado'),
        ];
    }
}
