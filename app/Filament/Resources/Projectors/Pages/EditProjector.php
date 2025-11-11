<?php

namespace App\Filament\Resources\Projectors\Pages;

use App\Filament\Resources\Projectors\ProjectorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProjector extends EditRecord
{
    protected static string $resource = ProjectorResource::class;

    protected static ?string $title = 'Editar Registro del Proyector';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar estabilizador actual
        $projector = $this->record;
        $components = $projector->components;

        $data['stabilizer_component_id'] = $components->firstWhere('componentable_type', 'App\Models\Stabilizer')?->id;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extraer el estabilizador
        $stabilizerComponentId = $data['stabilizer_component_id'] ?? null;

        // Eliminar del array de datos principales
        unset($data['stabilizer_component_id']);

        // Actualizar el proyector
        $record->update($data);

        // Actualizar estabilizador
        $this->syncStabilizer($record, $stabilizerComponentId);

        return $record;
    }

    protected function syncStabilizer(Model $projector, ?int $newStabilizerComponentId): void
    {
        // Obtener estabilizador actual
        $currentStabilizer = $projector->components()
            ->where('components.componentable_type', 'App\Models\Stabilizer')
            ->first();

        // Si hay un estabilizador actual, removerlo (marcar como removido)
        if ($currentStabilizer && $currentStabilizer->id !== $newStabilizerComponentId) {
            $projector->components()->updateExistingPivot($currentStabilizer->id, [
                'status' => 'Removido',
                'updated_at' => now()
            ]);
        }

        // Si hay un nuevo estabilizador, asignarlo
        if ($newStabilizerComponentId) {
            if ($currentStabilizer && $currentStabilizer->id === $newStabilizerComponentId) {
                // Ya está asignado, no hacer nada
                return;
            }

            // Asignar el nuevo estabilizador
            $projector->components()->attach($newStabilizerComponentId, [
                'assigned_at' => now(),
                'status' => 'Vigente'
            ]);
        }
    }
    
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
