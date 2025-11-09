<?php

namespace App\Filament\Resources\Printers\Pages;

use App\Filament\Resources\Printers\PrinterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrinter extends EditRecord
{
    protected static string $resource = PrinterResource::class;

    protected static ?string $title = 'Editar Registro de la Impresora';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
            ->label('Eliminar Registro'),
        ];
    }
}
