<?php

namespace App\Filament\Resources\GPUS\Pages;

use App\Filament\Resources\GPUS\GPUResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGPU extends EditRecord
{
    protected static string $resource = GPUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
