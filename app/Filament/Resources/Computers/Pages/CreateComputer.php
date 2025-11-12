<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateComputer extends CreateRecord
{

    protected static ?string $title = 'Registrar Computadora';

    protected static string $resource = ComputerResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extraer datos de componentes antes de crear el registro
        $componentData = [
            'motherboard' => $data['motherboard_component_id'] ?? null,
            'cpu' => $data['cpu_component_id'] ?? null,
            'gpu' => $data['gpu_component_id'] ?? null,
            'rams' => $data['rams'] ?? [],
            'roms' => $data['roms'] ?? [],
            'power_supply' => $data['power_supply_component_id'] ?? null,
            'tower_case' => $data['tower_case_component_id'] ?? null,
            'network_adapter' => $data['network_adapter_component_id'] ?? null,
            'monitors' => $data['monitors'] ?? [],
            'keyboard' => $data['keyboard_component_id'] ?? null,
            'mouse' => $data['mouse_component_id'] ?? null,
            'audio_device' => $data['audio_device_component_id'] ?? null,
            'stabilizer' => $data['stabilizer_component_id'] ?? null,
            'splitter' => $data['splitter_component_id'] ?? null,
        ];

        // Eliminar datos de componentes del array de datos principales
        unset($data['motherboard_component_id'], $data['cpu_component_id'], $data['gpu_component_id']);
        unset($data['rams'], $data['roms'], $data['monitors']);
        unset($data['power_supply_component_id'], $data['tower_case_component_id']);
        unset($data['network_adapter_component_id'], $data['keyboard_component_id']);
        unset($data['mouse_component_id'], $data['audio_device_component_id']);
        unset($data['stabilizer_component_id'], $data['splitter_component_id']);

        // Crear la computadora
        $computer = static::getModel()::create($data);

        // Asignar componentes a la computadora
        $this->attachComponents($computer, $componentData);

        return $computer;
    }

    protected function attachComponents(Model $computer, array $componentData): void
    {
        $pivotData = [
            'assigned_at' => now(),
            'status' => 'Vigente',
            'assigned_by' => Auth::id(),
        ];

        // Componentes individuales
        $singleComponents = [
            $componentData['motherboard'],
            $componentData['cpu'],
            $componentData['gpu'],
            $componentData['power_supply'],
            $componentData['tower_case'],
            $componentData['network_adapter'],
            $componentData['keyboard'],
            $componentData['mouse'],
            $componentData['audio_device'],
            $componentData['stabilizer'],
            $componentData['splitter'],
        ];

        foreach (array_filter($singleComponents) as $componentId) {
            if ($componentId) {
                $computer->components()->attach($componentId, $pivotData);
            }
        }

        // RAMs (múltiples)
        foreach ($componentData['rams'] as $ram) {
            if (isset($ram['component_id'])) {
                $computer->components()->attach($ram['component_id'], $pivotData);
            }
        }

        // ROMs (múltiples)
        foreach ($componentData['roms'] as $rom) {
            if (isset($rom['component_id'])) {
                $computer->components()->attach($rom['component_id'], $pivotData);
            }
        }

        // Monitores (múltiples)
        foreach ($componentData['monitors'] as $monitor) {
            if (isset($monitor['component_id'])) {
                $computer->components()->attach($monitor['component_id'], $pivotData);
            }
        }
    }
}
