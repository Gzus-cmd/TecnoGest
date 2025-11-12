<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditComputer extends EditRecord
{
    protected static string $resource = ComputerResource::class;

    protected static ?string $title = 'Editar Información General';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Solo devolver datos básicos, sin componentes
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Actualizar solo información básica
        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
