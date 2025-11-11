<?php

namespace App\Filament\Resources\Printers\Pages;

use App\Filament\Resources\Printers\PrinterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPrinter extends EditRecord
{
    protected static string $resource = PrinterResource::class;

    protected static ?string $title = 'Editar Registro de la Impresora';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar estabilizador actual
        $printer = $this->record;
        $components = $printer->components;

        $data['stabilizer_component_id'] = $components->firstWhere('componentable_type', 'App\Models\Stabilizer')?->id;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extraer el estabilizador
        $stabilizerComponentId = $data['stabilizer_component_id'] ?? null;

        // Eliminar del array de datos principales
        unset($data['stabilizer_component_id']);

        // Actualizar la impresora
        $record->update($data);

        // Actualizar estabilizador
        $this->syncStabilizer($record, $stabilizerComponentId);

        return $record;
    }

    protected function syncStabilizer(Model $printer, ?int $newStabilizerComponentId): void
    {
        // Obtener estabilizador actual
        $currentStabilizer = $printer->components()
            ->where('components.componentable_type', 'App\Models\Stabilizer')
            ->first();

        // Si hay un estabilizador actual, removerlo (marcar como removido)
        if ($currentStabilizer && $currentStabilizer->id !== $newStabilizerComponentId) {
            $printer->components()->updateExistingPivot($currentStabilizer->id, [
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
            $printer->components()->attach($newStabilizerComponentId, [
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
