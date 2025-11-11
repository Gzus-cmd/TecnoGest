<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use App\Models\Component;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditComputer extends EditRecord
{
    protected static string $resource = ComputerResource::class;

    protected static ?string $title = 'Editar Registro de Computadora';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar componentes actuales
        $computer = $this->record;
        $components = $computer->components;

        // Mapear componentes individuales
        $data['motherboard_component_id'] = $components->firstWhere('componentable_type', 'App\Models\Motherboard')?->id;
        $data['cpu_component_id'] = $components->firstWhere('componentable_type', 'App\Models\CPU')?->id;
        $data['gpu_component_id'] = $components->firstWhere('componentable_type', 'App\Models\GPU')?->id;
        $data['power_supply_component_id'] = $components->firstWhere('componentable_type', 'App\Models\PowerSupply')?->id;
        $data['tower_case_component_id'] = $components->firstWhere('componentable_type', 'App\Models\TowerCase')?->id;
        $data['network_adapter_component_id'] = $components->firstWhere('componentable_type', 'App\Models\NetworkAdapter')?->id;
        $data['keyboard_component_id'] = $components->firstWhere('componentable_type', 'App\Models\Keyboard')?->id;
        $data['mouse_component_id'] = $components->firstWhere('componentable_type', 'App\Models\Mouse')?->id;
        $data['audio_device_component_id'] = $components->firstWhere('componentable_type', 'App\Models\AudioDevice')?->id;
        $data['stabilizer_component_id'] = $components->firstWhere('componentable_type', 'App\Models\Stabilizer')?->id;
        $data['splitter_component_id'] = $components->firstWhere('componentable_type', 'App\Models\Splitter')?->id;

        // Mapear componentes múltiples (RAMs, ROMs, Monitores)
        $data['rams'] = $components->where('componentable_type', 'App\Models\RAM')
            ->map(fn($c) => ['component_id' => $c->id])->toArray();

        $data['roms'] = $components->where('componentable_type', 'App\Models\ROM')
            ->map(fn($c) => ['component_id' => $c->id])->toArray();

        $data['monitors'] = $components->where('componentable_type', 'App\Models\Monitor')
            ->map(fn($c) => ['component_id' => $c->id])->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extraer datos de componentes
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

        // Actualizar la computadora
        $record->update($data);

        // Marcar componentes actuales como removidos
        $record->components()->updateExistingPivot(
            $record->components->pluck('id'),
            ['status' => 'Removido']
        );

        // Asignar nuevos componentes
        $this->attachComponents($record, $componentData);

        return $record;
    }

    protected function attachComponents(Model $computer, array $componentData): void
    {
        $pivotData = ['assigned_at' => now(), 'status' => 'Vigente'];

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
                // Verificar si ya existe la relación
                $exists = $computer->allComponents()->wherePivot('component_id', $componentId)->exists();
                if ($exists) {
                    // Actualizar el pivot para marcar como vigente nuevamente
                    $computer->components()->updateExistingPivot($componentId, $pivotData);
                } else {
                    // Crear nueva relación
                    $computer->components()->attach($componentId, $pivotData);
                }
            }
        }

        // RAMs (múltiples)
        foreach ($componentData['rams'] as $ram) {
            if (isset($ram['component_id'])) {
                $exists = $computer->allComponents()->wherePivot('component_id', $ram['component_id'])->exists();
                if ($exists) {
                    $computer->components()->updateExistingPivot($ram['component_id'], $pivotData);
                } else {
                    $computer->components()->attach($ram['component_id'], $pivotData);
                }
            }
        }

        // ROMs (múltiples)
        foreach ($componentData['roms'] as $rom) {
            if (isset($rom['component_id'])) {
                $exists = $computer->allComponents()->wherePivot('component_id', $rom['component_id'])->exists();
                if ($exists) {
                    $computer->components()->updateExistingPivot($rom['component_id'], $pivotData);
                } else {
                    $computer->components()->attach($rom['component_id'], $pivotData);
                }
            }
        }

        // Monitores (múltiples)
        foreach ($componentData['monitors'] as $monitor) {
            if (isset($monitor['component_id'])) {
                $exists = $computer->allComponents()->wherePivot('component_id', $monitor['component_id'])->exists();
                if ($exists) {
                    $computer->components()->updateExistingPivot($monitor['component_id'], $pivotData);
                } else {
                    $computer->components()->attach($monitor['component_id'], $pivotData);
                }
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('actualizarHardware')
                ->label('Actualizar Hardware')
                ->icon('heroicon-o-cpu-chip')
                ->color('warning')
                ->modalHeading('Actualizar Componentes de Hardware')
                ->modalDescription('Reemplace o agregue componentes a esta computadora')
                ->modalWidth('5xl')
                ->form([
                    Select::make('motherboard_component_id')
                        ->label('Placa Base')
                        ->options(function () {
                            $current = $this->record->components->firstWhere('componentable_type', 'App\Models\Motherboard');
                            
                            $available = Component::where('componentable_type', 'App\Models\Motherboard')
                                ->where('status', 'Operativo')
                                ->whereDoesntHave('computers')
                                ->get()
                                ->mapWithKeys(function ($component) {
                                    $mb = $component->componentable;
                                    return [$component->id => "{$mb->brand} {$mb->model} - Serial: {$component->serial}"];
                                });

                            if ($current) {
                                $mb = $current->componentable;
                                $available->prepend("{$mb->brand} {$mb->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                            }

                            return $available;
                        })
                        ->default(fn() => $this->record->components->firstWhere('componentable_type', 'App\Models\Motherboard')?->id)
                        ->searchable()
                        ->live(),

                    Select::make('cpu_component_id')
                        ->label('Procesador (CPU)')
                        ->options(function (Get $get) {
                            $motherboardComponentId = $get('motherboard_component_id');
                            $current = $this->record->components->firstWhere('componentable_type', 'App\Models\CPU');

                            if (!$motherboardComponentId) {
                                return $current ? [$current->id => "{$current->componentable->brand} {$current->componentable->model} (ACTUAL)"] : [];
                            }

                            $mbComponent = Component::find($motherboardComponentId);
                            if (!$mbComponent) {
                                return [];
                            }

                            $motherboard = $mbComponent->componentable;
                            $socket = $motherboard->socket;

                            $available = Component::where('componentable_type', 'App\Models\CPU')
                                ->where('status', 'Operativo')
                                ->whereDoesntHave('computers')
                                ->get()
                                ->filter(function ($component) use ($socket) {
                                    return $component->componentable->socket === $socket;
                                })
                                ->mapWithKeys(function ($component) {
                                    $cpu = $component->componentable;
                                    return [$component->id => "{$cpu->brand} {$cpu->model} ({$cpu->socket}) - Serial: {$component->serial}"];
                                });

                            if ($current && $current->componentable->socket === $socket) {
                                $cpu = $current->componentable;
                                $available->prepend("{$cpu->brand} {$cpu->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                            }

                            return $available;
                        })
                        ->default(fn() => $this->record->components->firstWhere('componentable_type', 'App\Models\CPU')?->id)
                        ->searchable(),

                    Repeater::make('rams')
                        ->label('Memorias RAM')
                        ->schema([
                            Select::make('component_id')
                                ->label('RAM')
                                ->options(function () {
                                    // Obtener IDs de RAMs actuales de esta computadora
                                    $currentRamIds = $this->record->components()
                                        ->where('components.componentable_type', 'App\Models\RAM')
                                        ->pluck('components.id')
                                        ->toArray();
                                    
                                    return Component::where('componentable_type', 'App\Models\RAM')
                                        ->where('status', 'Operativo')
                                        ->where(function ($query) use ($currentRamIds) {
                                            $query->whereDoesntHave('computers')
                                                ->orWhereIn('id', $currentRamIds);
                                        })
                                        ->get()
                                        ->mapWithKeys(function ($component) use ($currentRamIds) {
                                            $ram = $component->componentable;
                                            $label = "{$ram->brand} {$ram->model} - {$ram->capacity}GB - Serial: {$component->serial}";
                                            if (in_array($component->id, $currentRamIds)) {
                                                $label .= " (ACTUAL)";
                                            }
                                            return [$component->id => $label];
                                        });
                                })
                                ->searchable()
                                ->required()
                                ->distinct(),
                        ])
                        ->default(function () {
                            return $this->record->components
                                ->where('componentable_type', 'App\Models\RAM')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray();
                        })
                        ->minItems(1)
                        ->addActionLabel('Agregar RAM')
                        ->collapsible(),

                    Repeater::make('roms')
                        ->label('Almacenamiento')
                        ->schema([
                            Select::make('component_id')
                                ->label('ROM')
                                ->options(function () {
                                    // Obtener IDs de ROMs actuales de esta computadora
                                    $currentRomIds = $this->record->components()
                                        ->where('components.componentable_type', 'App\Models\ROM')
                                        ->pluck('components.id')
                                        ->toArray();
                                    
                                    return Component::where('componentable_type', 'App\Models\ROM')
                                        ->where('status', 'Operativo')
                                        ->where(function ($query) use ($currentRomIds) {
                                            $query->whereDoesntHave('computers')
                                                ->orWhereIn('id', $currentRomIds);
                                        })
                                        ->get()
                                        ->mapWithKeys(function ($component) use ($currentRomIds) {
                                            $rom = $component->componentable;
                                            $label = "{$rom->brand} {$rom->model} - {$rom->capacity}GB - Serial: {$component->serial}";
                                            if (in_array($component->id, $currentRomIds)) {
                                                $label .= " (ACTUAL)";
                                            }
                                            return [$component->id => $label];
                                        });
                                })
                                ->searchable()
                                ->required()
                                ->distinct(),
                        ])
                        ->default(function () {
                            return $this->record->components
                                ->where('componentable_type', 'App\Models\ROM')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray();
                        })
                        ->minItems(1)
                        ->addActionLabel('Agregar Almacenamiento')
                        ->collapsible(),
                ])
                ->action(function (array $data) {
                    $this->handleHardwareUpdate($data);
                }),
            
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

    protected function handleHardwareUpdate(array $data): void
    {
        $componentData = [
            'motherboard' => $data['motherboard_component_id'] ?? null,
            'cpu' => $data['cpu_component_id'] ?? null,
            'rams' => $data['rams'] ?? [],
            'roms' => $data['roms'] ?? [],
        ];

        // Marcar componentes afectados como removidos
        $typesToRemove = ['App\Models\Motherboard', 'App\Models\CPU', 'App\Models\RAM', 'App\Models\ROM'];
        $this->record->components()
            ->whereIn('componentable_type', $typesToRemove)
            ->updateExistingPivot(
                $this->record->components()->whereIn('componentable_type', $typesToRemove)->pluck('components.id'),
                ['status' => 'Removido']
            );

        // Asignar nuevos componentes
        $this->attachComponents($this->record, $componentData);

        Notification::make()
            ->title('Hardware actualizado')
            ->success()
            ->body('Los componentes de hardware han sido actualizados exitosamente.')
            ->send();
    }
}
