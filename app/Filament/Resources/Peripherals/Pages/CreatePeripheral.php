<?php

namespace App\Filament\Resources\Peripherals\Pages;

use App\Filament\Resources\PeripheralResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePeripheral extends CreateRecord
{
    protected static string $resource = PeripheralResource::class;

    protected static ?string $title = 'Crear Periférico';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extraer componentes antes de crear el periférico
        $this->componentData = [
            'keyboard' => $data['keyboard_component_id'] ?? null,
            'mouse' => $data['mouse_component_id'] ?? null,
            'audio_device' => $data['audio_device_component_id'] ?? null,
            'stabilizer' => $data['stabilizer_component_id'] ?? null,
            'splitter' => $data['splitter_component_id'] ?? null,
            'monitors' => $data['monitors'] ?? [],
        ];

        // Remover los componentes del array de datos
        unset($data['keyboard_component_id']);
        unset($data['mouse_component_id']);
        unset($data['audio_device_component_id']);
        unset($data['stabilizer_component_id']);
        unset($data['splitter_component_id']);
        unset($data['monitors']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Asignar componentes después de crear el periférico
        $peripheral = $this->record;
        $componentData = $this->componentData;

        $pivotData = [
            'assigned_at' => now(),
            'status' => 'Vigente',
            'assigned_by' => Auth::id(),
        ];

        // Componentes individuales
        $singleComponents = [
            $componentData['keyboard'],
            $componentData['mouse'],
            $componentData['audio_device'],
            $componentData['stabilizer'],
            $componentData['splitter'],
        ];

        foreach ($singleComponents as $componentId) {
            if ($componentId) {
                $peripheral->components()->attach($componentId, $pivotData);
            }
        }

        // Monitores
        foreach ($componentData['monitors'] as $monitor) {
            if (isset($monitor['component_id'])) {
                $peripheral->components()->attach($monitor['component_id'], $pivotData);
            }
        }
    }

    private $componentData = [];
}
