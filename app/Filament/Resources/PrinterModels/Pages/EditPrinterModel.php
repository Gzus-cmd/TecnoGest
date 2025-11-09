<?php

namespace App\Filament\Resources\PrinterModels\Pages;

use App\Filament\Resources\PrinterModels\PrinterModelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrinterModel extends EditRecord
{
    protected static string $resource = PrinterModelResource::class;

    protected static ?string $title = 'Editar Registros de Modelos de Impresoras';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
            ->label('Eliminar Registro'),
        ];
    }
}
