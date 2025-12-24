<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use App\Models\Peripheral;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateComputer extends CreateRecord
{

    protected static ?string $title = 'Registrar Computadora';

    protected static string $resource = ComputerResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Extraer datos de componentes de CPU
            $cpuComponentData = [
                'motherboard' => $data['motherboard_component_id'] ?? null,
                'cpu' => $data['cpu_component_id'] ?? null,
                'gpu' => $data['gpu_component_id'] ?? null,
                'rams' => $data['rams'] ?? [],
                'roms' => $data['roms'] ?? [],
                'power_supply' => $data['power_supply_component_id'] ?? null,
                'tower_case' => $data['tower_case_component_id'] ?? null,
                'network_adapter' => $data['network_adapter_component_id'] ?? null,
            ];

            // Extraer datos de componentes periféricos
            $peripheralComponentData = [
                'monitors' => $data['monitors'] ?? [],
                'keyboard' => $data['keyboard_component_id'] ?? null,
                'mouse' => $data['mouse_component_id'] ?? null,
                'audio' => $data['audio_component_id'] ?? null,
                'stabilizer' => $data['stabilizer_component_id'] ?? null,
                'splitter' => $data['splitter_component_id'] ?? null,
            ];

            // Verificar si hay componentes periféricos
            $hasPeripherals = !empty(array_filter($peripheralComponentData['monitors'])) ||
                            $peripheralComponentData['keyboard'] ||
                            $peripheralComponentData['mouse'] ||
                            $peripheralComponentData['audio'] ||
                            $peripheralComponentData['stabilizer'] ||
                            $peripheralComponentData['splitter'];

            // Limpiar datos antes de crear Computer
            unset($data['motherboard_component_id'], $data['cpu_component_id'], $data['gpu_component_id']);
            unset($data['rams'], $data['roms']);
            unset($data['power_supply_component_id'], $data['tower_case_component_id']);
            unset($data['network_adapter_component_id']);
            unset($data['monitors'], $data['keyboard_component_id'], $data['mouse_component_id']);
            unset($data['audio_component_id'], $data['stabilizer_component_id'], $data['splitter_component_id']);

            // Crear la computadora (CPU)
            $computer = static::getModel()::create($data);

            // Asignar componentes de CPU
            $this->attachComponents($computer, $cpuComponentData);

            // Si hay periféricos, crear Peripheral y asignar componentes
            if ($hasPeripherals) {
                $peripheral = $this->createPeripheral($computer, $peripheralComponentData);
                $computer->update(['peripheral_id' => $peripheral->id]);
            }

            return $computer;
        });
    }

    protected function createPeripheral(Model $computer, array $peripheralComponentData): Peripheral
    {
        // Obtener el siguiente código de periférico
        $lastPeripheral = Peripheral::latest('id')->first();
        $nextNumber = $lastPeripheral ? ($lastPeripheral->id + 1) : 1;
        $code = 'PER-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Crear el periférico
        $peripheral = Peripheral::create([
            'code' => $code,
            'location_id' => $computer->location_id,
            'computer_id' => $computer->id,
            'notes' => 'Creado junto con Computer #' . $computer->id,
        ]);

        // Asignar componentes periféricos
        $pivotData = [
            'assigned_at' => now(),
            'status' => 'Vigente',
            'assigned_by' => Auth::id(),
        ];

        // Monitores (múltiples)
        foreach ($peripheralComponentData['monitors'] as $monitor) {
            if (isset($monitor['component_id'])) {
                $peripheral->components()->attach($monitor['component_id'], $pivotData);
            }
        }

        // Componentes individuales
        $singleComponents = [
            $peripheralComponentData['keyboard'],
            $peripheralComponentData['mouse'],
            $peripheralComponentData['audio'],
            $peripheralComponentData['stabilizer'],
            $peripheralComponentData['splitter'],
        ];

        foreach (array_filter($singleComponents) as $componentId) {
            if ($componentId) {
                $peripheral->components()->attach($componentId, $pivotData);
            }
        }

        return $peripheral;
    }

    protected function attachComponents(Model $computer, array $componentData): void
    {
        $pivotData = [
            'assigned_at' => now(),
            'status' => 'Vigente',
            'assigned_by' => Auth::id(),
        ];

        // Componentes individuales de CPU
        $singleComponents = [
            $componentData['motherboard'],
            $componentData['cpu'],
            $componentData['gpu'],
            $componentData['power_supply'],
            $componentData['tower_case'],
            $componentData['network_adapter'],
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
    }
}
