<?php

namespace App\Filament\Resources\Projectors\Pages;

use App\Filament\Resources\Projectors\ProjectorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProjector extends EditRecord
{
    protected static string $resource = ProjectorResource::class;

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
