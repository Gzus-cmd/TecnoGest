<?php

namespace App\Filament\Resources\PrinterModels\Pages;

use App\Filament\Resources\PrinterModels\PrinterModelResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrinterModel extends CreateRecord
{
    protected static ?string $title = 'Registrar Modelo de Impresora';

    protected static string $resource = PrinterModelResource::class;

}
