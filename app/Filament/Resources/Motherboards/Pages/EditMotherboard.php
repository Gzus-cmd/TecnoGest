<?php

namespace App\Filament\Resources\Motherboards\Pages;

use App\Filament\Resources\Motherboards\MotherboardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMotherboard extends EditRecord
{
    protected static string $resource = MotherboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
