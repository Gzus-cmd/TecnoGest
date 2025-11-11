<?php

namespace App\Filament\Resources\CPUS\Pages;

use App\Filament\Resources\CPUS\CPUResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCPU extends EditRecord
{
    protected static string $resource = CPUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
