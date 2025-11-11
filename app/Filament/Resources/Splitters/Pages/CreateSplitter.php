<?php

namespace App\Filament\Resources\Splitters\Pages;

use App\Filament\Resources\Splitters\SplitterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSplitter extends CreateRecord
{
    protected static ?string $title = 'Registrar Distribuidor';

    protected static string $resource = SplitterResource::class;
}
