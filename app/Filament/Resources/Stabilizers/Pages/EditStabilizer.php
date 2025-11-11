<?php

namespace App\Filament\Resources\Stabilizers\Pages;

use App\Filament\Resources\Stabilizers\StabilizerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStabilizer extends EditRecord
{
    protected static string $resource = StabilizerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
