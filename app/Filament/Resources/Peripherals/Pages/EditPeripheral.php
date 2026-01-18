<?php

namespace App\Filament\Resources\Peripherals\Pages;

use App\Filament\Resources\PeripheralResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPeripheral extends EditRecord
{
    protected static string $resource = PeripheralResource::class;

    protected static ?string $title = 'Editar Periférico';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar componentes actuales
        $peripheral = $this->record;
        
        $data['keyboard_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Keyboard')?->id;
        $data['mouse_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Mouse')?->id;
        $data['audio_device_component_id'] = $peripheral->components->firstWhere('componentable_type', 'AudioDevice')?->id;
        $data['stabilizer_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Stabilizer')?->id;
        $data['splitter_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Splitter')?->id;
        $data['monitors'] = $peripheral->components
            ->where('componentable_type', 'Monitor')
            ->map(fn($c) => ['component_id' => $c->id])
            ->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extraer componentes antes de guardar
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

    protected function afterSave(): void
    {
        // Actualizar componentes después de guardar
        $peripheral = $this->record;
        $componentData = $this->componentData;

        $currentComponents = [
            'keyboard' => $peripheral->components->firstWhere('componentable_type', 'Keyboard')?->id,
            'mouse' => $peripheral->components->firstWhere('componentable_type', 'Mouse')?->id,
            'audio_device' => $peripheral->components->firstWhere('componentable_type', 'AudioDevice')?->id,
            'stabilizer' => $peripheral->components->firstWhere('componentable_type', 'Stabilizer')?->id,
            'splitter' => $peripheral->components->firstWhere('componentable_type', 'Splitter')?->id,
            'monitors' => $peripheral->components->where('componentable_type', 'Monitor')->pluck('id')->toArray(),
        ];

        $componentsToRemove = [];

        foreach (['keyboard', 'mouse', 'audio_device', 'stabilizer', 'splitter'] as $type) {
            $currentId = $currentComponents[$type];
            $newId = $componentData[$type];
            
            if ($currentId && $currentId != $newId) {
                $componentsToRemove[] = $currentId;
            }
        }

        $newMonitorIds = array_column($componentData['monitors'], 'component_id');
        foreach ($currentComponents['monitors'] as $currentMonitorId) {
            if (!in_array($currentMonitorId, $newMonitorIds)) {
                $componentsToRemove[] = $currentMonitorId;
            }
        }

        if (!empty($componentsToRemove)) {
            $peripheral->components()->updateExistingPivot($componentsToRemove, [
                'status' => 'Removido',
                'removed_by' => Auth::id(),
            ]);
        }

        $pivotData = [
            'assigned_at' => now(),
            'status' => 'Vigente',
            'assigned_by' => Auth::id(),
        ];

        $singleComponents = [
            $componentData['keyboard'],
            $componentData['mouse'],
            $componentData['audio_device'],
            $componentData['stabilizer'],
            $componentData['splitter'],
        ];

        foreach ($singleComponents as $componentId) {
            if ($componentId) {
                $exists = $peripheral->allComponents()->wherePivot('component_id', $componentId)->exists();
                if ($exists) {
                    $peripheral->components()->updateExistingPivot($componentId, $pivotData);
                } else {
                    $peripheral->components()->attach($componentId, $pivotData);
                }
            }
        }

        foreach ($componentData['monitors'] as $monitor) {
            if (isset($monitor['component_id'])) {
                $exists = $peripheral->allComponents()->wherePivot('component_id', $monitor['component_id'])->exists();
                if ($exists) {
                    $peripheral->components()->updateExistingPivot($monitor['component_id'], $pivotData);
                } else {
                    $peripheral->components()->attach($monitor['component_id'], $pivotData);
                }
            }
        }
    }

    private $componentData = [];
}
