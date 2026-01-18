<?php

namespace App\Filament\Resources\Computers\Tables;

use App\Models\Component;
use App\Models\OS;
use App\Models\Monitor;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\AudioDevice;
use App\Models\Stabilizer;
use App\Models\Splitter;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;

class ComputersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial')
                    ->label('Codigo')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->label('Departamento')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Inactivo' => 'gray',
                        'En Mantenimiento' => 'warning',
                        'Activo' => 'success',
                        'Desmantelado' => 'danger',
                    }),
                TextColumn::make('ip_address')
                    ->label('Dirección IP')
                    ->searchable(),
                TextColumn::make('os.name')
                    ->label('Sistema Operativo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('peripheral.code')
                    ->label('Periférico')
                    ->default('Sin asignar')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'Inactivo' => 'Inactivo',
                        'En Mantenimiento' => 'En Mantenimiento',
                        'Activo' => 'Activo',
                        'Desmantelado' => 'Desmantelado',
                    ]),

            ])
            ->recordActions([
                Action::make('asignarPeriferico')
                    ->label('Asignar')
                    ->icon('heroicon-o-computer-desktop')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        auth()->user()?->can('ComputerAssignPeripheral') &&
                            $record->peripheral_id === null &&
                            in_array($record->status, ['Activo', 'Inactivo'])
                    )
                    ->modalHeading('Asignar Periférico a Computadora')
                    ->modalDescription(fn($record) => "Seleccione un periférico disponible para asignar a {$record->serial}. La computadora se trasladará a la ubicación del periférico y ambos se activarán.")
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Asignar')
                    ->modalCancelActionLabel('Cancelar')
                    ->form([
                        Select::make('peripheral_id')
                            ->label('Periférico Disponible')
                            ->options(function ($record) {
                                // Solo periféricos SIN PC asignada
                                return \App\Models\Peripheral::whereNull('computer_id')
                                    ->with(['components.componentable', 'location'])
                                    ->get()
                                    ->mapWithKeys(function ($peripheral) {
                                        $locationName = $peripheral->location ? $peripheral->location->name : 'Sin ubicación';
                                        return [$peripheral->id => "{$peripheral->code} | {$locationName}"];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->helperText('Solo se muestran periféricos inactivos sin PC asignada'),
                    ])
                    ->action(function ($record, array $data): void {
                        $peripheral = \App\Models\Peripheral::find($data['peripheral_id']);

                        if (!$peripheral) {
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body('Periférico no encontrado.')
                                ->send();
                            return;
                        }

                        DB::transaction(function () use ($record, $peripheral) {
                            $originalComputerLocation = $record->location_id;

                            // Mover LA PC a la ubicación del periférico (no al revés)
                            $record->update([
                                'location_id' => $peripheral->location_id,
                                'peripheral_id' => $peripheral->id,
                                'status' => 'Activo',
                            ]);

                            // Asignar PC al periférico
                            $peripheral->update([
                                'computer_id' => $record->id,
                            ]);

                            // Crear registro de traslado de la PC (si cambió de ubicación)
                            if ($originalComputerLocation != $peripheral->location_id) {
                                \App\Models\Transfer::create([
                                    'deviceable_type' => \App\Models\Computer::class,
                                    'deviceable_id' => $record->id,
                                    'registered_by' => Auth::id(),
                                    'origin_id' => $originalComputerLocation,
                                    'destiny_id' => $peripheral->location_id,
                                    'date' => now()->format('Y-m-d'),
                                    'reason' => "Traslado para asignación con periférico {$peripheral->code}",
                                    'status' => 'Finalizado',
                                ]);
                            }
                        });

                        Notification::make()
                            ->title('Periférico asignado')
                            ->success()
                            ->body("El periférico {$peripheral->code} ha sido asignado a {$record->serial}. Ambos están ahora activos en {$peripheral->location->name}.")
                            ->send();
                    }),

                Action::make('desmantelar')
                    ->label('Desmantelar')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Desmantelar Computadora')
                    ->modalDescription(fn($record) => "¿Está seguro de desmantelar la computadora {$record->serial}? Todos los componentes vigentes serán removidos y la computadora pasará al estado 'Desmantelado'.")
                    ->modalSubmitActionLabel('Sí, desmantelar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(
                        fn($record) =>
                        auth()->user()?->can('ComputerDismantle') &&
                            $record->status === 'Inactivo'
                    )
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // Actualizar todos los componentes vigentes a "Removido"
                            DB::table('componentables')
                                ->where('componentable_type', 'App\\Models\\Computer')
                                ->where('componentable_id', $record->id)
                                ->where('status', 'Vigente')
                                ->update([
                                    'status' => 'Removido',
                                    'updated_at' => now()
                                ]);

                            // Cambiar el estado de la computadora a Desmantelado
                            $record->update(['status' => 'Desmantelado']);
                        });

                        Notification::make()
                            ->title('Computadora desmantelada')
                            ->success()
                            ->body("La computadora {$record->serial} ha sido desmantelada exitosamente.")
                            ->send();
                    }),

                Action::make('actualizarSistema')
                    ->label('Actualizar')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->visible(
                        fn($record) =>
                        auth()->user()?->can('ComputerUpdateSystem') &&
                            $record->status === 'En Mantenimiento'
                    )
                    ->modalHeading('Actualizar')
                    ->modalDescription('Modifique los componentes de la computadora')
                    ->modalWidth('6xl')
                    ->modalSubmitActionLabel('Guardar Cambios')
                    ->modalCancelActionLabel('Cancelar')
                    ->form([

                        Grid::make(2)->schema([
                            Select::make('motherboard_component_id')
                                ->label('Placa Base')
                                ->options(function ($record) {
                                    $record->load('components.componentable');
                                    $current = $record->components->firstWhere('componentable_type', 'Motherboard');

                                    $available = Component::where('componentable_type', 'Motherboard')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('computers')
                                        ->with('componentable')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $mb = $component->componentable;
                                            $brand = $mb->brand ?? 'N/A';
                                            $model = $mb->model ?? 'N/A';
                                            return [$component->id => "{$brand} {$model} - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $mb = $current->componentable;
                                        $brand = $mb->brand ?? 'N/A';
                                        $model = $mb->model ?? 'N/A';
                                        $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->default(fn($record) => $record->components->firstWhere('componentable_type', 'Motherboard')?->id)
                                ->searchable()
                                ->live(),

                            Select::make('cpu_component_id')
                                ->label('Procesador (CPU)')
                                ->options(function (Get $get, $record) {
                                    $motherboardComponentId = $get('motherboard_component_id');
                                    $record->load('components.componentable');
                                    $current = $record->components->firstWhere('componentable_type', 'CPU');

                                    // Si no hay placa base seleccionada, mostrar el actual
                                    if (!$motherboardComponentId) {
                                        if ($current) {
                                            $cpu = $current->componentable;
                                            $brand = $cpu->brand ?? 'N/A';
                                            $model = $cpu->model ?? 'N/A';
                                            return [$current->id => "{$brand} {$model} - Serial: {$current->serial} (ACTUAL)"];
                                        }
                                        return [];
                                    }

                                    $mbComponent = Component::with('componentable')->find($motherboardComponentId);
                                    if (!$mbComponent) {
                                        return [];
                                    }

                                    $motherboard = $mbComponent->componentable;
                                    $socket = $motherboard->socket;

                                    $available = Component::where('componentable_type', 'CPU')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('computers')
                                        ->with('componentable')
                                        ->get()
                                        ->filter(function ($component) use ($socket) {
                                            return $component->componentable->socket === $socket;
                                        })
                                        ->mapWithKeys(function ($component) {
                                            $cpu = $component->componentable;
                                            $brand = $cpu->brand ?? 'N/A';
                                            $model = $cpu->model ?? 'N/A';
                                            $socket = $cpu->socket ?? 'N/A';
                                            return [$component->id => "{$brand} {$model} ({$socket}) - Serial: {$component->serial}"];
                                        });

                                    if ($current && $current->componentable->socket === $socket) {
                                        $cpu = $current->componentable;
                                        $brand = $cpu->brand ?? 'N/A';
                                        $model = $cpu->model ?? 'N/A';
                                        $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->getOptionLabelUsing(function ($value) {
                                    if (!$value) return null;
                                    $component = Component::with('componentable')->find($value);
                                    if (!$component || !$component->componentable) return $value;
                                    $cpu = $component->componentable;
                                    $brand = $cpu->brand ?? 'N/A';
                                    $model = $cpu->model ?? 'N/A';
                                    return "{$brand} {$model} - Serial: {$component->serial}";
                                })
                                ->default(fn($record) => $record->components->firstWhere('componentable_type', 'CPU')?->id)
                                ->searchable(),
                        ]),
                        Select::make('gpu_component_id')
                            ->label('Tarjeta Gráfica (GPU)')
                            ->options(function ($record) {
                                $record->load('components.componentable');
                                $current = $record->components->firstWhere('componentable_type', 'GPU');

                                $available = Component::where('componentable_type', 'GPU')
                                    ->where('status', 'Operativo')
                                    ->whereDoesntHave('computers')
                                    ->with('componentable')
                                    ->get()
                                    ->mapWithKeys(function ($component) {
                                        $gpu = $component->componentable;
                                        $brand = $gpu->brand ?? 'N/A';
                                        $model = $gpu->model ?? 'N/A';
                                        $vram = $gpu->vram ?? 'N/A';
                                        return [$component->id => "{$brand} {$model} - {$vram}GB - Serial: {$component->serial}"];
                                    });

                                if ($current) {
                                    $gpu = $current->componentable;
                                    $brand = $gpu->brand ?? 'N/A';
                                    $model = $gpu->model ?? 'N/A';
                                    $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                }

                                return $available;
                            })
                            ->default(fn($record) => $record->components->firstWhere('componentable_type', 'GPU')?->id)
                            ->searchable(),

                        Grid::make(2)->schema([
                            Repeater::make('rams')
                                ->label('Memorias RAM')
                                ->schema([
                                    Select::make('component_id')
                                        ->label('RAM')
                                        ->options(function ($record) {
                                            // Obtener IDs de componentes RAM actualmente asignados a esta computadora
                                            $currentRamIds = $record->components()
                                                ->where('components.componentable_type', 'RAM')
                                                ->pluck('components.id')
                                                ->toArray();

                                            // Obtener todos los componentes RAM operativos que:
                                            // 1. No están asignados a ninguna computadora (whereDoesntHave)
                                            // 2. O están asignados a ESTA computadora
                                            $availableRams = Component::where('componentable_type', 'RAM')
                                                ->where('status', 'Operativo')
                                                ->where(function ($query) use ($currentRamIds) {
                                                    $query->whereDoesntHave('computers')
                                                        ->orWhereIn('id', $currentRamIds);
                                                })
                                                ->with('componentable')
                                                ->get();

                                            return $availableRams->mapWithKeys(function ($component) use ($currentRamIds) {
                                                $ram = $component->componentable;
                                                $brand = $ram->brand ?? 'N/A';
                                                $model = $ram->model ?? 'N/A';
                                                $capacity = $ram->capacity ?? 'N/A';
                                                $label = "{$brand} {$model} - {$capacity}GB - Serial: {$component->serial}";
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
                                ->minItems(1)
                                ->addActionLabel('Agregar RAM')
                                ->collapsible()
                                ->collapsed(),

                            Repeater::make('roms')
                                ->label('Almacenamiento')
                                ->schema([
                                    Select::make('component_id')
                                        ->label('ROM')
                                        ->options(function ($record) {
                                            // Obtener IDs de componentes ROM actualmente asignados a esta computadora
                                            $currentRomIds = $record->components()
                                                ->where('components.componentable_type', 'ROM')
                                                ->pluck('components.id')
                                                ->toArray();

                                            // Obtener todos los componentes ROM operativos que:
                                            // 1. No están asignados a ninguna computadora
                                            // 2. O están asignados a ESTA computadora
                                            $availableRoms = Component::where('componentable_type', 'ROM')
                                                ->where('status', 'Operativo')
                                                ->where(function ($query) use ($currentRomIds) {
                                                    $query->whereDoesntHave('computers')
                                                        ->orWhereIn('id', $currentRomIds);
                                                })
                                                ->with('componentable')
                                                ->get();

                                            return $availableRoms->mapWithKeys(function ($component) use ($currentRomIds) {
                                                $rom = $component->componentable;
                                                $brand = $rom->brand ?? 'N/A';
                                                $model = $rom->model ?? 'N/A';
                                                $capacity = $rom->capacity ?? 'N/A';
                                                $label = "{$brand} {$model} - {$capacity}GB - Serial: {$component->serial}";
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
                                ->minItems(1)
                                ->addActionLabel('Agregar Almacenamiento')
                                ->collapsible()
                                ->collapsed(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('power_supply_component_id')
                                ->label('Fuente de Poder')
                                ->options(function ($record) {
                                    $record->load('components.componentable');
                                    $current = $record->components->firstWhere('componentable_type', 'PowerSupply');

                                    $available = Component::where('componentable_type', 'PowerSupply')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('computers')
                                        ->with('componentable')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $ps = $component->componentable;
                                            $brand = $ps->brand ?? 'N/A';
                                            $model = $ps->model ?? 'N/A';
                                            $power = $ps->power ?? 'N/A';
                                            return [$component->id => "{$brand} {$model} - {$power}W - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $ps = $current->componentable;
                                        $brand = $ps->brand ?? 'N/A';
                                        $model = $ps->model ?? 'N/A';
                                        $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->default(fn($record) => $record->components->firstWhere('componentable_type', 'PowerSupply')?->id)
                                ->searchable(),

                            Select::make('tower_case_component_id')
                                ->label('Gabinete/Case')
                                ->options(function ($record) {
                                    $record->load('components.componentable');
                                    $current = $record->components->firstWhere('componentable_type', 'TowerCase');

                                    $available = Component::where('componentable_type', 'TowerCase')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('computers')
                                        ->with('componentable')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $case = $component->componentable;
                                            $brand = $case->brand ?? 'N/A';
                                            $model = $case->model ?? 'N/A';
                                            return [$component->id => "{$brand} {$model} - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $case = $current->componentable;
                                        $brand = $case->brand ?? 'N/A';
                                        $model = $case->model ?? 'N/A';
                                        $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->default(fn($record) => $record->components->firstWhere('componentable_type', 'TowerCase')?->id)
                                ->searchable(),
                        ]),

                        Section::make('Periféricos')
                            ->description('Componentes externos (opcional)')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Repeater::make('monitors')
                                    ->label('Monitores')
                                    ->schema([
                                        Select::make('component_id')
                                            ->label('Monitor')
                                            ->options(function ($record) {
                                                $record->load('peripheral.components.componentable');
                                                $peripheral = $record->peripheral;
                                                if (!$peripheral) {
                                                    return Component::where('componentable_type', 'Monitor')
                                                        ->where('status', 'Operativo')
                                                        ->whereDoesntHave('peripheral')
                                                        ->with('componentable')
                                                        ->get()
                                                        ->mapWithKeys(function ($component) {
                                                            $monitor = $component->componentable;
                                                            $brand = $monitor->brand ?? 'N/A';
                                                            $model = $monitor->model ?? 'N/A';
                                                            $screenSize = $monitor->screen_size ?? 'N/A';
                                                            return [$component->id => "{$brand} {$model} - {$screenSize}\" - Serial: {$component->serial}"];
                                                        });
                                                }

                                                $currentMonitorIds = $peripheral->components()
                                                    ->where('components.componentable_type', 'Monitor')
                                                    ->pluck('components.id')
                                                    ->toArray();

                                                $available = Component::where('componentable_type', 'Monitor')
                                                    ->where('status', 'Operativo')
                                                    ->where(function ($query) use ($currentMonitorIds) {
                                                        $query->whereDoesntHave('peripheral')
                                                            ->orWhereIn('id', $currentMonitorIds);
                                                    })
                                                    ->with('componentable')
                                                    ->get();

                                                return $available->mapWithKeys(function ($component) use ($currentMonitorIds) {
                                                    $monitor = $component->componentable;
                                                    $brand = $monitor->brand ?? 'N/A';
                                                    $model = $monitor->model ?? 'N/A';
                                                    $screenSize = $monitor->screen_size ?? 'N/A';
                                                    $label = "{$brand} {$model} - {$screenSize}\" - Serial: {$component->serial}";
                                                    if (in_array($component->id, $currentMonitorIds)) {
                                                        $label .= " (ACTUAL)";
                                                    }
                                                    return [$component->id => $label];
                                                });
                                            })
                                            ->searchable()
                                            ->required()
                                            ->distinct(),
                                    ])
                                    ->addActionLabel('Agregar Monitor')
                                    ->collapsible()
                                    ->defaultItems(0),

                                Grid::make(2)->schema([
                                    Select::make('keyboard_component_id')
                                        ->label('Teclado')
                                        ->options(function ($record) {
                                            $record->load('peripheral.components.componentable');
                                            $peripheral = $record->peripheral;
                                            $current = $peripheral?->components->firstWhere('componentable_type', 'Keyboard');

                                            $available = Component::where('componentable_type', 'Keyboard')
                                                ->where('status', 'Operativo')
                                                ->whereDoesntHave('peripheral')
                                                ->with('componentable')
                                                ->get()
                                                ->mapWithKeys(function ($component) {
                                                    $kb = $component->componentable;
                                                    $brand = $kb->brand ?? 'N/A';
                                                    $model = $kb->model ?? 'N/A';
                                                    return [$component->id => "{$brand} {$model} - Serial: {$component->serial}"];
                                                });

                                            if ($current) {
                                                $kb = $current->componentable;
                                                $brand = $kb->brand ?? 'N/A';
                                                $model = $kb->model ?? 'N/A';
                                                $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                            }

                                            return $available;
                                        })
                                        ->searchable(),

                                    Select::make('mouse_component_id')
                                        ->label('Mouse')
                                        ->options(function ($record) {
                                            $record->load('peripheral.components.componentable');
                                            $peripheral = $record->peripheral;
                                            $current = $peripheral?->components->firstWhere('componentable_type', 'Mouse');

                                            $available = Component::where('componentable_type', 'Mouse')
                                                ->where('status', 'Operativo')
                                                ->whereDoesntHave('peripheral')
                                                ->with('componentable')
                                                ->get()
                                                ->mapWithKeys(function ($component) {
                                                    $mouse = $component->componentable;
                                                    $brand = $mouse->brand ?? 'N/A';
                                                    $model = $mouse->model ?? 'N/A';
                                                    return [$component->id => "{$brand} {$model} - Serial: {$component->serial}"];
                                                });

                                            if ($current) {
                                                $mouse = $current->componentable;
                                                $brand = $mouse->brand ?? 'N/A';
                                                $model = $mouse->model ?? 'N/A';
                                                $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                            }

                                            return $available;
                                        })
                                        ->searchable(),

                                    Select::make('audio_component_id')
                                        ->label('Dispositivo de Audio')
                                        ->options(function ($record) {
                                            $record->load('peripheral.components.componentable');
                                            $peripheral = $record->peripheral;
                                            $current = $peripheral?->components->firstWhere('componentable_type', 'AudioDevice');

                                            $available = Component::where('componentable_type', 'AudioDevice')
                                                ->where('status', 'Operativo')
                                                ->whereDoesntHave('peripheral')
                                                ->with('componentable')
                                                ->get()
                                                ->mapWithKeys(function ($component) {
                                                    $audio = $component->componentable;
                                                    $brand = $audio->brand ?? 'N/A';
                                                    $model = $audio->model ?? 'N/A';
                                                    $type = $audio->type ?? 'N/A';
                                                    return [$component->id => "{$brand} {$model} ({$type}) - Serial: {$component->serial}"];
                                                });

                                            if ($current) {
                                                $audio = $current->componentable;
                                                $brand = $audio->brand ?? 'N/A';
                                                $model = $audio->model ?? 'N/A';
                                                $type = $audio->type ?? 'N/A';
                                                $available->prepend("{$brand} {$model} ({$type}) - Serial: {$current->serial} (ACTUAL)", $current->id);
                                            }

                                            return $available;
                                        })
                                        ->searchable(),

                                    Select::make('stabilizer_component_id')
                                        ->label('Estabilizador')
                                        ->options(function ($record) {
                                            $record->load('peripheral.components.componentable');
                                            $peripheral = $record->peripheral;
                                            $current = $peripheral?->components->firstWhere('componentable_type', 'Stabilizer');

                                            $available = Component::where('componentable_type', 'Stabilizer')
                                                ->where('status', 'Operativo')
                                                ->whereDoesntHave('peripheral')
                                                ->with('componentable')
                                                ->get()
                                                ->mapWithKeys(function ($component) {
                                                    $stab = $component->componentable;
                                                    $brand = $stab->brand ?? 'N/A';
                                                    $model = $stab->model ?? 'N/A';
                                                    $capacity = $stab->capacity ?? 'N/A';
                                                    return [$component->id => "{$brand} {$model} - {$capacity}VA - Serial: {$component->serial}"];
                                                });

                                            if ($current) {
                                                $stab = $current->componentable;
                                                $brand = $stab->brand ?? 'N/A';
                                                $model = $stab->model ?? 'N/A';
                                                $capacity = $stab->capacity ?? 'N/A';
                                                $available->prepend("{$brand} {$model} - {$capacity}VA - Serial: {$current->serial} (ACTUAL)", $current->id);
                                            }

                                            return $available;
                                        })
                                        ->searchable(),

                                    Select::make('splitter_component_id')
                                        ->label('Splitter')
                                        ->options(function ($record) {
                                            $record->load('peripheral.components.componentable');
                                            $peripheral = $record->peripheral;
                                            $current = $peripheral?->components->firstWhere('componentable_type', 'Splitter');

                                            $available = Component::where('componentable_type', 'Splitter')
                                                ->where('status', 'Operativo')
                                                ->whereDoesntHave('peripheral')
                                                ->with('componentable')
                                                ->get()
                                                ->mapWithKeys(function ($component) {
                                                    $splitter = $component->componentable;
                                                    $brand = $splitter->brand ?? 'N/A';
                                                    $model = $splitter->model ?? 'N/A';
                                                    $ports = $splitter->ports ?? 'N/A';
                                                    return [$component->id => "{$brand} {$model} - {$ports} puertos - Serial: {$component->serial}"];
                                                });

                                            if ($current) {
                                                $splitter = $current->componentable;
                                                $brand = $splitter->brand ?? 'N/A';
                                                $model = $splitter->model ?? 'N/A';
                                                $ports = $splitter->ports ?? 'N/A';
                                                $available->prepend("{$brand} {$model} - {$ports} puertos - Serial: {$current->serial} (ACTUAL)", $current->id);
                                            }

                                            return $available;
                                        })
                                        ->searchable(),
                                ]),
                            ]),
                    ])
                    ->fillForm(function ($record): array {
                        $peripheral = $record->peripheral;

                        $data = [
                            'motherboard_component_id' => $record->components->firstWhere('componentable_type', 'Motherboard')?->id,
                            'cpu_component_id' => $record->components->firstWhere('componentable_type', 'CPU')?->id,
                            'gpu_component_id' => $record->components->firstWhere('componentable_type', 'GPU')?->id,
                            'power_supply_component_id' => $record->components->firstWhere('componentable_type', 'PowerSupply')?->id,
                            'tower_case_component_id' => $record->components->firstWhere('componentable_type', 'TowerCase')?->id,
                            'network_adapter_component_id' => $record->components->firstWhere('componentable_type', 'NetworkAdapter')?->id,
                            'rams' => $record->components
                                ->where('componentable_type', 'RAM')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray(),
                            'roms' => $record->components
                                ->where('componentable_type', 'ROM')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray(),
                        ];

                        // Cargar periféricos si existen
                        if ($peripheral) {
                            $data['monitors'] = $peripheral->components
                                ->where('componentable_type', 'Monitor')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray();
                            $data['keyboard_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Keyboard')?->id;
                            $data['mouse_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Mouse')?->id;
                            $data['audio_component_id'] = $peripheral->components->firstWhere('componentable_type', 'AudioDevice')?->id;
                            $data['stabilizer_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Stabilizer')?->id;
                            $data['splitter_component_id'] = $peripheral->components->firstWhere('componentable_type', 'Splitter')?->id;
                        }

                        return $data;
                    })
                    ->action(function ($record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            // Actualizar solo componentes internos de hardware
                            $componentData = [
                                'motherboard' => $data['motherboard_component_id'] ?? null,
                                'cpu' => $data['cpu_component_id'] ?? null,
                                'gpu' => $data['gpu_component_id'] ?? null,
                                'power_supply' => $data['power_supply_component_id'] ?? null,
                                'tower_case' => $data['tower_case_component_id'] ?? null,
                                'network_adapter' => $data['network_adapter_component_id'] ?? null,
                                'rams' => $data['rams'] ?? [],
                                'roms' => $data['roms'] ?? [],
                            ];

                            // Obtener componentes actuales para comparar
                            $currentComponents = [
                                'motherboard' => $record->components->firstWhere('componentable_type', 'Motherboard')?->id,
                                'cpu' => $record->components->firstWhere('componentable_type', 'CPU')?->id,
                                'gpu' => $record->components->firstWhere('componentable_type', 'GPU')?->id,
                                'power_supply' => $record->components->firstWhere('componentable_type', 'PowerSupply')?->id,
                                'tower_case' => $record->components->firstWhere('componentable_type', 'TowerCase')?->id,
                                'network_adapter' => $record->components->firstWhere('componentable_type', 'NetworkAdapter')?->id,
                                'rams' => $record->components->where('componentable_type', 'RAM')->pluck('id')->toArray(),
                                'roms' => $record->components->where('componentable_type', 'ROM')->pluck('id')->toArray(),
                            ];

                            // Identificar componentes que fueron REMOVIDOS (estaban antes pero ya no están)
                            $componentsToRemove = [];

                            // Componentes individuales
                            foreach (['motherboard', 'cpu', 'gpu', 'power_supply', 'tower_case', 'network_adapter'] as $type) {
                                $currentId = $currentComponents[$type];
                                $newId = $componentData[$type];

                                // Si había uno y cambió (o se quitó), marcarlo como removido
                                if ($currentId && $currentId != $newId) {
                                    $componentsToRemove[] = $currentId;
                                }
                            }

                            // RAMs - marcar como removidos los que ya no están en la lista
                            $newRamIds = array_column($componentData['rams'], 'component_id');
                            foreach ($currentComponents['rams'] as $currentRamId) {
                                if (!in_array($currentRamId, $newRamIds)) {
                                    $componentsToRemove[] = $currentRamId;
                                }
                            }

                            // ROMs - marcar como removidos los que ya no están en la lista
                            $newRomIds = array_column($componentData['roms'], 'component_id');
                            foreach ($currentComponents['roms'] as $currentRomId) {
                                if (!in_array($currentRomId, $newRomIds)) {
                                    $componentsToRemove[] = $currentRomId;
                                }
                            }

                            // Marcar SOLO los componentes que realmente fueron removidos
                            if (!empty($componentsToRemove)) {
                                $record->components()->updateExistingPivot($componentsToRemove, [
                                    'status' => 'Removido',
                                    'removed_by' => Auth::id(),
                                ]);
                            }

                            // Asignar nuevos componentes o actualizar los que se mantienen
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
                            ];

                            foreach ($singleComponents as $componentId) {
                                if ($componentId) {
                                    $exists = $record->allComponents()->wherePivot('component_id', $componentId)->exists();
                                    if ($exists) {
                                        $record->components()->updateExistingPivot($componentId, $pivotData);
                                    } else {
                                        $record->components()->attach($componentId, $pivotData);
                                    }
                                }
                            }

                            // RAMs
                            foreach ($componentData['rams'] as $ram) {
                                if (isset($ram['component_id'])) {
                                    $exists = $record->allComponents()->wherePivot('component_id', $ram['component_id'])->exists();
                                    if ($exists) {
                                        $record->components()->updateExistingPivot($ram['component_id'], $pivotData);
                                    } else {
                                        $record->components()->attach($ram['component_id'], $pivotData);
                                    }
                                }
                            }

                            // ROMs
                            foreach ($componentData['roms'] as $rom) {
                                if (isset($rom['component_id'])) {
                                    $exists = $record->allComponents()->wherePivot('component_id', $rom['component_id'])->exists();
                                    if ($exists) {
                                        $record->components()->updateExistingPivot($rom['component_id'], $pivotData);
                                    } else {
                                        $record->components()->attach($rom['component_id'], $pivotData);
                                    }
                                }
                            }

                            // Manejar periféricos
                            $peripheralComponentData = [
                                'monitors' => $data['monitors'] ?? [],
                                'keyboard' => $data['keyboard_component_id'] ?? null,
                                'mouse' => $data['mouse_component_id'] ?? null,
                                'audio' => $data['audio_component_id'] ?? null,
                                'stabilizer' => $data['stabilizer_component_id'] ?? null,
                                'splitter' => $data['splitter_component_id'] ?? null,
                            ];

                            $hasPeripherals = !empty(array_filter($peripheralComponentData['monitors'])) ||
                                $peripheralComponentData['keyboard'] ||
                                $peripheralComponentData['mouse'] ||
                                $peripheralComponentData['audio'] ||
                                $peripheralComponentData['stabilizer'] ||
                                $peripheralComponentData['splitter'];

                            if ($hasPeripherals) {
                                if ($record->peripheral) {
                                    // Actualizar peripheral existente
                                    $peripheral = $record->peripheral;

                                    // Desvincular componentes actuales
                                    $peripheral->components()->wherePivot('status', 'Vigente')->update([
                                        'componentables.status' => 'Removido',
                                        'componentables.removed_by' => Auth::id(),
                                    ]);

                                    // Asignar nuevos componentes
                                    foreach ($peripheralComponentData['monitors'] as $monitor) {
                                        if (isset($monitor['component_id'])) {
                                            $peripheral->components()->attach($monitor['component_id'], $pivotData);
                                        }
                                    }

                                    $singlePeripheralComponents = [
                                        $peripheralComponentData['keyboard'],
                                        $peripheralComponentData['mouse'],
                                        $peripheralComponentData['audio'],
                                        $peripheralComponentData['stabilizer'],
                                        $peripheralComponentData['splitter'],
                                    ];

                                    foreach (array_filter($singlePeripheralComponents) as $componentId) {
                                        if ($componentId) {
                                            $peripheral->components()->attach($componentId, $pivotData);
                                        }
                                    }
                                } else {
                                    // Crear nuevo peripheral
                                    $lastPeripheral = \App\Models\Peripheral::latest('id')->first();
                                    $nextNumber = $lastPeripheral ? ($lastPeripheral->id + 1) : 1;
                                    $code = 'PER-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                                    $peripheral = \App\Models\Peripheral::create([
                                        'code' => $code,
                                        'location_id' => $record->location_id,
                                        'computer_id' => $record->id,
                                        'notes' => 'Creado desde Actualizar Computer #' . $record->id,
                                    ]);

                                    // Asignar componentes
                                    foreach ($peripheralComponentData['monitors'] as $monitor) {
                                        if (isset($monitor['component_id'])) {
                                            $peripheral->components()->attach($monitor['component_id'], $pivotData);
                                        }
                                    }

                                    $singlePeripheralComponents = [
                                        $peripheralComponentData['keyboard'],
                                        $peripheralComponentData['mouse'],
                                        $peripheralComponentData['audio'],
                                        $peripheralComponentData['stabilizer'],
                                        $peripheralComponentData['splitter'],
                                    ];

                                    foreach (array_filter($singlePeripheralComponents) as $componentId) {
                                        if ($componentId) {
                                            $peripheral->components()->attach($componentId, $pivotData);
                                        }
                                    }

                                    $record->update(['peripheral_id' => $peripheral->id]);
                                }
                            } else {
                                // Si no hay periféricos y existía uno, desvincularlo
                                if ($record->peripheral) {
                                    $record->peripheral->update(['computer_id' => null]);
                                    $record->update(['peripheral_id' => null]);
                                }
                            }
                        }); // Fin de DB::transaction

                        Notification::make()
                            ->title('Actualizado')
                            ->success()
                            ->body('Componentes actualizados exitosamente.')
                            ->send();
                    }),



                Action::make('verComponentes')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn() => auth()->user()?->can('ComputerViewComponents'))
                    ->modalHeading('Detalles de la Computadora')
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->infolist(function ($record) {
                        // Cargar componentes con sus relaciones
                        $record->load(['components.componentable', 'os', 'peripheral.components.componentable']);

                        // Componentes internos del CPU
                        $motherboard = $record->components->firstWhere('componentable_type', 'Motherboard');
                        $cpu = $record->components->firstWhere('componentable_type', 'CPU');
                        $gpu = $record->components->firstWhere('componentable_type', 'GPU');
                        $powerSupply = $record->components->firstWhere('componentable_type', 'PowerSupply');
                        $towerCase = $record->components->firstWhere('componentable_type', 'TowerCase');
                        $networkAdapter = $record->components->firstWhere('componentable_type', 'NetworkAdapter');
                        $rams = $record->components->where('componentable_type', 'RAM');
                        $roms = $record->components->where('componentable_type', 'ROM');

                        // Componentes periféricos (ahora desde peripheral)
                        $peripheral = $record->peripheral;
                        $keyboard = $peripheral?->components->firstWhere('componentable_type', 'Keyboard');
                        $mouse = $peripheral?->components->firstWhere('componentable_type', 'Mouse');
                        $audioDevice = $peripheral?->components->firstWhere('componentable_type', 'AudioDevice');
                        $stabilizer = $peripheral?->components->firstWhere('componentable_type', 'Stabilizer');
                        $splitter = $peripheral?->components->firstWhere('componentable_type', 'Splitter');
                        $monitors = $peripheral?->components->where('componentable_type', 'Monitor') ?? collect();

                        return [
                            // SECCIÓN: SOFTWARE Y RED
                            ViewEntry::make('software_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '💻 Software y Red',
                                    'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),

                            Section::make()
                                ->schema([
                                    TextEntry::make('os_info')
                                        ->label('Sistema Operativo')
                                        ->state(function () use ($record) {
                                            if (!$record->os) return 'No asignado';
                                            $os = $record->os;
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Nombre:</span> <span style='color: #9ca3af;'>{$os->name}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Versión:</span> <span style='color: #9ca3af;'>" . ($os->version ?? 'N/A') . "</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Arquitectura:</span> <span style='color: #9ca3af;'>" . ($os->architecture ?? 'N/A') . "</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Licencia:</span> <span style='color: #9ca3af;'>" . ($os->license_key ?? 'N/A') . "</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('ip_address')
                                        ->label('Dirección IP')
                                        ->state(function () use ($record) {
                                            return "<span style='color: #9ca3af;'>" . ($record->ip_address ?? 'No asignada') . "</span>";
                                        })
                                        ->html(),

                                    TextEntry::make('peripheral_info')
                                        ->label('Periféricos Asignados')
                                        ->state(function () use ($peripheral) {
                                            if (!$peripheral) return "<span style='color: #9ca3af;'>Sin periféricos asignados</span>";
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div><span style='font-weight: 700; color: #10b981;'>Código:</span> <span style='color: #9ca3af;'>{$peripheral->code}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(2),

                            // SECCIÓN: HARDWARE PRINCIPAL
                            ViewEntry::make('hardware_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '🔧 Hardware Principal',
                                    'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),

                            Section::make()
                                ->schema([
                                    TextEntry::make('motherboard_info')
                                        ->label('Placa Base')
                                        ->state(function () use ($motherboard) {
                                            if (!$motherboard) return '<span style="color: #9ca3af;">No asignada</span>';
                                            $mb = $motherboard->componentable;
                                            $brand = $mb->brand ?? 'N/A';
                                            $model = $mb->model ?? 'N/A';
                                            $socket = $mb->socket ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Socket:</span> <span style='color: #9ca3af;'>{$socket}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$motherboard->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$motherboard->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('cpu_info')
                                        ->label('Procesador (CPU)')
                                        ->state(function () use ($cpu) {
                                            if (!$cpu) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $c = $cpu->componentable;
                                            $brand = $c->brand ?? 'N/A';
                                            $model = $c->model ?? 'N/A';
                                            $frequency = $c->frequency ?? 'N/A';
                                            $socket = $c->socket ?? 'N/A';
                                            $cores = $c->cores ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Socket:</span> <span style='color: #9ca3af;'>{$socket}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Frecuencia:</span> <span style='color: #9ca3af;'>{$frequency} GHz</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Núcleos:</span> <span style='color: #9ca3af;'>{$cores}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$cpu->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$cpu->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('gpu_info')
                                        ->label('Tarjeta Gráfica (GPU)')
                                        ->state(function () use ($gpu) {
                                            if (!$gpu) return '<span style="color: #9ca3af;">No asignada</span>';
                                            $g = $gpu->componentable;
                                            $brand = $g->brand ?? 'N/A';
                                            $model = $g->model ?? 'N/A';
                                            $vram = $g->vram ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>VRAM:</span> <span style='color: #9ca3af;'>{$vram} GB</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$gpu->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$gpu->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('power_supply_info')
                                        ->label('Fuente de Poder')
                                        ->state(function () use ($powerSupply) {
                                            if (!$powerSupply) return '<span style="color: #9ca3af;">No asignada</span>';
                                            $ps = $powerSupply->componentable;
                                            $brand = $ps->brand ?? 'N/A';
                                            $model = $ps->model ?? 'N/A';
                                            $power = $ps->power ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Potencia:</span> <span style='color: #9ca3af;'>{$power} W</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$powerSupply->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$powerSupply->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('tower_case_info')
                                        ->label('Gabinete/Case')
                                        ->state(function () use ($towerCase) {
                                            if (!$towerCase) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $tc = $towerCase->componentable;
                                            $brand = $tc->brand ?? 'N/A';
                                            $model = $tc->model ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$towerCase->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$towerCase->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('network_adapter_info')
                                        ->label('Adaptador de Red')
                                        ->state(function () use ($networkAdapter) {
                                            if (!$networkAdapter) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $na = $networkAdapter->componentable;
                                            $brand = $na->brand ?? 'N/A';
                                            $model = $na->model ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$networkAdapter->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$networkAdapter->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(3),

                            // SECCIÓN: MEMORIA Y ALMACENAMIENTO
                            ViewEntry::make('memoria_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '💾 Memoria y Almacenamiento',
                                    'gradient' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                                    'textColor' => '#1f2937'
                                ])
                                ->columnSpanFull(),

                            Section::make()
                                ->schema([
                                    TextEntry::make('rams_info')
                                        ->label('Memorias RAM')
                                        ->state(function () use ($rams) {
                                            if ($rams->isEmpty()) return '<span style="color: #9ca3af;">No hay memorias RAM asignadas</span>';
                                            return $rams->map(function ($ram, $index) {
                                                $r = $ram->componentable;
                                                $num = $index + 1;
                                                $brand = $r->brand ?? 'N/A';
                                                $model = $r->model ?? 'N/A';
                                                $frequency = $r->frequency ?? 'N/A';
                                                $capacity = $r->capacity ?? 'N/A';
                                                $type = $r->type ?? 'N/A';
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid #6366f1;'>" .
                                                    "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>RAM #{$num}: {$brand} {$model}</div>" .
                                                    "<div style='line-height: 1.6;'>" .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Capacidad:</span> <span style='color: #9ca3af;'>{$capacity} GB</span> | " .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Tipo:</span> <span style='color: #9ca3af;'>{$type}</span> | " .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Frecuencia:</span> <span style='color: #9ca3af;'>{$frequency} MHz</span><br>" .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$ram->serial}</span> | " .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$ram->status}</span>" .
                                                    "</div></div>";
                                            })->join('');
                                        })
                                        ->html()
                                        ->columnSpanFull(),

                                    TextEntry::make('roms_info')
                                        ->label('Almacenamiento (ROMs)')
                                        ->state(function () use ($roms) {
                                            if ($roms->isEmpty()) return '<span style="color: #9ca3af;">No hay almacenamiento asignado</span>';
                                            return $roms->map(function ($rom, $index) {
                                                $r = $rom->componentable;
                                                $num = $index + 1;
                                                $brand = $r->brand ?? 'N/A';
                                                $model = $r->model ?? 'N/A';
                                                $capacity = $r->capacity ?? 'N/A';
                                                $type = $r->type ?? 'N/A';
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid #8b5cf6;'>" .
                                                    "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>Almacenamiento #{$num}: {$brand} {$model}</div>" .
                                                    "<div style='line-height: 1.6;'>" .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Capacidad:</span> <span style='color: #9ca3af;'>{$capacity} GB</span> | " .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Tipo:</span> <span style='color: #9ca3af;'>{$type}</span><br>" .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$rom->serial}</span> | " .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$rom->status}</span>" .
                                                    "</div></div>";
                                            })->join('');
                                        })
                                        ->html()
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),

                            // SECCIÓN: PERIFÉRICOS
                            ViewEntry::make('perifericos_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '🖥️ Periféricos',
                                    'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),

                            Section::make()
                                ->schema([
                                    TextEntry::make('monitors_info')
                                        ->label('Monitores')
                                        ->state(function () use ($monitors) {
                                            if ($monitors->isEmpty()) return '<span style="color: #9ca3af;">No hay monitores asignados</span>';
                                            return $monitors->map(function ($monitor, $index) {
                                                $m = $monitor->componentable;
                                                $brand = $m->brand ?? 'N/A';
                                                $model = $m->model ?? 'N/A';
                                                $screenSize = $m->screen_size ?? 'N/A';
                                                $num = $index + 1;
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid #10b981;'>" .
                                                    "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>Monitor #{$num}: {$brand} {$model}</div>" .
                                                    "<div style='line-height: 1.6;'>" .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Tamaño:</span> <span style='color: #9ca3af;'>{$screenSize} pulgadas</span><br>" .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$monitor->serial}</span> | " .
                                                    "<span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$monitor->status}</span>" .
                                                    "</div></div>";
                                            })->join('');
                                        })
                                        ->html()
                                        ->columnSpanFull(),

                                    TextEntry::make('keyboard_info')
                                        ->label('Teclado')
                                        ->state(function () use ($keyboard) {
                                            if (!$keyboard) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $kb = $keyboard->componentable;
                                            $brand = $kb->brand ?? 'N/A';
                                            $model = $kb->model ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$keyboard->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$keyboard->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('mouse_info')
                                        ->label('Mouse')
                                        ->state(function () use ($mouse) {
                                            if (!$mouse) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $m = $mouse->componentable;
                                            $brand = $m->brand ?? 'N/A';
                                            $model = $m->model ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$mouse->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$mouse->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('audio_device_info')
                                        ->label('Dispositivo de Audio')
                                        ->state(function () use ($audioDevice) {
                                            if (!$audioDevice) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $ad = $audioDevice->componentable;
                                            $brand = $ad->brand ?? 'N/A';
                                            $model = $ad->model ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$audioDevice->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$audioDevice->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('stabilizer_info')
                                        ->label('Estabilizador')
                                        ->state(function () use ($stabilizer) {
                                            if (!$stabilizer) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $st = $stabilizer->componentable;
                                            $brand = $st->brand ?? 'N/A';
                                            $model = $st->model ?? 'N/A';
                                            $power = $st->power ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Potencia:</span> <span style='color: #9ca3af;'>{$power} VA</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$stabilizer->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$stabilizer->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('splitter_info')
                                        ->label('Multicontacto/Splitter')
                                        ->state(function () use ($splitter) {
                                            if (!$splitter) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $sp = $splitter->componentable;
                                            $brand = $sp->brand ?? 'N/A';
                                            $model = $sp->model ?? 'N/A';
                                            $outlets = $sp->outlets ?? 'N/A';
                                            return "<div style='line-height: 1.8;'>" .
                                                "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$brand} {$model}</div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Número de Tomas:</span> <span style='color: #9ca3af;'>{$outlets}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$splitter->serial}</span></div>" .
                                                "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$splitter->status}</span></div>" .
                                                "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(3),
                        ];
                    }),

                ActionGroup::make([
                    Action::make('verHistorial')
                        ->label('Historial')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->visible(fn() => auth()->user()?->can('ComputerViewHistory'))
                        ->modalHeading('Historial de la Computadora')
                        ->modalDescription(fn($record) => "Seleccione el tipo de historial que desea consultar para {$record->serial}")
                        ->modalIcon('heroicon-o-clock')
                        ->modalWidth('md')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar')
                        ->extraModalFooterActions([
                            Action::make('historialComponentes')
                                ->label('Historial de Componentes')
                                ->icon('heroicon-o-cpu-chip')
                                ->color('info')
                                ->url(fn($record): string => route('filament.admin.resources.component-histories.index', [
                                    'filters' => [
                                        'device_id' => ['value' => 'Computer-' . $record->id],
                                    ],
                                ]))
                                ->openUrlInNewTab(),

                            Action::make('historialMantenimientos')
                                ->label('Historial de Mantenimientos')
                                ->icon('heroicon-o-wrench-screwdriver')
                                ->color('warning')
                                ->url(fn($record): string => route('filament.admin.resources.maintenances.index', [
                                    'filters' => [
                                        'deviceable_type' => ['value' => 'Computer'],
                                        'deviceable_id' => ['value' => $record->id],
                                    ],
                                ]))
                                ->openUrlInNewTab(),

                            Action::make('historialTraslados')
                                ->label('Historial de Traslados')
                                ->icon('heroicon-o-arrow-path')
                                ->color('success')
                                ->url(fn($record): string => route('filament.admin.resources.transfers.index', [
                                    'filters' => [
                                        'deviceable_type' => ['value' => 'Computer'],
                                        'deviceable_id' => ['value' => $record->id],
                                    ],
                                ]))
                                ->openUrlInNewTab(),

                            Action::make('generarReporte')
                                ->label('Generar Reporte Completo')
                                ->icon('heroicon-o-document-arrow-down')
                                ->color('danger')
                                ->visible(fn() => auth()->user()?->can('ComputerGenerateReport'))
                                ->url(fn($record): string => route('devices.full-report', [
                                    'type' => 'computer',
                                    'id' => $record->id,
                                ])),
                        ]),
                    EditAction::make()
                        ->label('Editar'),
                    DeleteAction::make()
                        ->label('Eliminar'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('primary')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de computadoras');
    }
}
