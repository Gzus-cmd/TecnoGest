<?php

namespace App\Filament\Resources\PrinterModels\Pages;

use App\Filament\Resources\PrinterModels\PrinterModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrinterModels extends ListRecords
{
    protected static string $resource = PrinterModelResource::class;

    protected static ?string $title = 'Lista de Modelos de Impresoras';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Registrar Modelo'),
        ];
    }
}
