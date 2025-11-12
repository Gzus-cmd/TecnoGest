<?php

namespace App\Filament\Resources\ComponentHistories\Pages;

use App\Filament\Resources\ComponentHistories\ComponentHistoryResource;
use App\Exports\ComponentHistoryExport;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ManageComponentHistories extends ManageRecords
{
    protected static string $resource = ComponentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
