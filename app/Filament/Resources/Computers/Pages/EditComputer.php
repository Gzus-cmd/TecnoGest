<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComputer extends EditRecord
{
    protected static string $resource = ComputerResource::class;

    protected static ?string $title = 'Editar Registro de Computadora';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
            ->label('Eliminar Registro'),
        ];
    }
}
