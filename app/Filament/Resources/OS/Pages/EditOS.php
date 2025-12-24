<?php

namespace App\Filament\Resources\OS\Pages;

use App\Filament\Resources\OS\OSResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOS extends EditRecord
{
    protected static string $resource = OSResource::class;

    protected static ?string $title = 'Editar Registro del Sistema Operativo';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
