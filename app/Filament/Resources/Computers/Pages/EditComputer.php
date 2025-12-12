<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use App\Models\Peripheral;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditComputer extends EditRecord
{
    protected static string $resource = ComputerResource::class;

    protected static ?string $title = 'Editar Computadora';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $computer = $this->getRecord();
        $peripheral = $computer->peripheral;

        // Cargar componentes periféricos si existen
        if ($peripheral) {
            $data['monitors'] = $peripheral->components()
                ->where('components.componentable_type', 'App\Models\Monitor')
                ->get()
                ->map(fn($component) => ['component_id' => $component->id])
                ->toArray();

            $keyboard = $peripheral->components()->where('components.componentable_type', 'App\Models\Keyboard')->first();
            $data['keyboard_component_id'] = $keyboard?->id;

            $mouse = $peripheral->components()->where('components.componentable_type', 'App\Models\Mouse')->first();
            $data['mouse_component_id'] = $mouse?->id;

            $audio = $peripheral->components()->where('components.componentable_type', 'App\Models\AudioDevice')->first();
            $data['audio_component_id'] = $audio?->id;

            $stabilizer = $peripheral->components()->where('components.componentable_type', 'App\Models\Stabilizer')->first();
            $data['stabilizer_component_id'] = $stabilizer?->id;

            $splitter = $peripheral->components()->where('components.componentable_type', 'App\Models\Splitter')->first();
            $data['splitter_component_id'] = $splitter?->id;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
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

            // Limpiar datos de periféricos antes de actualizar Computer
            unset($data['monitors'], $data['keyboard_component_id'], $data['mouse_component_id']);
            unset($data['audio_component_id'], $data['stabilizer_component_id'], $data['splitter_component_id']);

            // Actualizar Computer
            $record->update($data);

            // Manejar periféricos
            if ($hasPeripherals) {
                if ($record->peripheral) {
                    // Actualizar peripheral existente
                    $this->updatePeripheral($record->peripheral, $peripheralComponentData);
                } else {
                    // Crear nuevo peripheral
                    $peripheral = $this->createPeripheral($record, $peripheralComponentData);
                    $record->update(['peripheral_id' => $peripheral->id]);
                }
            } else {
                // Si no hay periféricos y existía uno, desvincularlo (no eliminarlo)
                if ($record->peripheral) {
                    $record->peripheral->update(['computer_id' => null]);
                    $record->update(['peripheral_id' => null]);
                }
            }

            return $record;
        });
    }

    protected function createPeripheral(Model $computer, array $peripheralComponentData): Peripheral
    {
        $lastPeripheral = Peripheral::latest('id')->first();
        $nextNumber = $lastPeripheral ? ($lastPeripheral->id + 1) : 1;
        $code = 'PER-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $peripheral = Peripheral::create([
            'code' => $code,
            'location_id' => $computer->location_id,
            'computer_id' => $computer->id,
            'notes' => 'Creado desde edición de Computer #' . $computer->id,
        ]);

        $this->attachPeripheralComponents($peripheral, $peripheralComponentData);

        return $peripheral;
    }

    protected function updatePeripheral(Peripheral $peripheral, array $peripheralComponentData): void
    {
        // Desvincular componentes actuales
        $peripheral->components()->wherePivot('status', 'Vigente')->update([
            'componentables.status' => 'Retirado',
            'componentables.removed_by' => Auth::id(),
        ]);

        // Asignar nuevos componentes
        $this->attachPeripheralComponents($peripheral, $peripheralComponentData);
    }

    protected function attachPeripheralComponents(Peripheral $peripheral, array $peripheralComponentData): void
    {
        $pivotData = [
            'assigned_at' => now(),
            'status' => 'Vigente',
            'assigned_by' => Auth::id(),
        ];

        // Monitores
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
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }
}
