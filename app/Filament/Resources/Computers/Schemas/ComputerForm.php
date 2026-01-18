<?php

namespace App\Filament\Resources\Computers\Schemas;

use App\Models\Component;
use App\Models\CPU;
use App\Models\GPU;
use App\Models\Location;
use App\Models\Motherboard;
use App\Models\RAM;
use App\Models\ROM;
use App\Models\NetworkAdapter;
use App\Models\PowerSupply;
use App\Models\TowerCase;
use App\Models\Monitor;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\AudioDevice;
use App\Models\Stabilizer;
use App\Models\Splitter;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ComputerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos de la computadora')
                    ->schema([
                        TextInput::make('serial')
                            ->label('Código/Serial')
                            ->required()
                            ->placeholder('COMP-001')
                            ->unique(ignoreRecord: true),
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'Activo' => 'Activo',
                                        'Inactivo' => 'Inactivo',
                                    ])
                                    ->required()
                                    ->reactive(),
                                Select::make('location_id')
                                    ->label('Departamento')
                                    ->options(function (Get $get) {
                                        $status = $get('status');
                                        
                                        $query = Location::query();
                                        
                                        // Si está Inactivo, solo mostrar talleres de informática
                                        if ($status === 'Inactivo') {
                                            $query->where('is_workshop', true);
                                        }
                                        
                                        return $query->get()
                                            ->mapWithKeys(fn ($location) => [$location->id => "{$location->pavilion} | {$location->name}"]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText(fn (Get $get) => $get('status') === 'Inactivo' 
                                        ? 'Dispositivos inactivos solo pueden ir a talleres de informática' 
                                        : 'Puede seleccionar cualquier ubicación'),
                            ]),
                    ]),

                Section::make('Información del Sistema')
                    ->description('Configuración de red y sistema operativo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ip_address')
                                    ->label('Dirección IP')
                                    ->placeholder('192.168.1.100'),
                                Select::make('os_id')
                                    ->label('Sistema Operativo')
                                    ->relationship('os', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} {$record->version} ({$record->architecture})")
                                    ->searchable(['name', 'version', 'architecture'])
                                    ->preload()
                                    ->required(),
                            ]),
                    ]),

                Section::make('Asignación de Periféricos')
                    ->description('Asigne un conjunto de periféricos (opcional)')
                    ->schema([
                        Select::make('peripheral_id')
                            ->label('Conjunto de Periféricos')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = \App\Models\Peripheral::query();
                                
                                // Si estamos editando, incluir el periférico actual O disponibles
                                if ($currentRecord && $currentRecord->peripheral_id) {
                                    $query->where(function ($q) use ($currentRecord) {
                                        $q->whereNull('computer_id')
                                          ->orWhere('id', $currentRecord->peripheral_id);
                                    });
                                } else {
                                    // Si estamos creando, solo disponibles
                                    $query->whereNull('computer_id');
                                }
                                
                                return $query->with('location')->get()->mapWithKeys(function ($peripheral) use ($currentRecord) {
                                    $location = $peripheral->location ? " - {$peripheral->location->name}" : '';
                                    return [$peripheral->id => "{$peripheral->code}{$location}"];
                                });
                            })
                            ->searchable()
                            ->nullable()
                            ->placeholder('Sin asignar')
                            ->helperText('Seleccione un conjunto de periféricos disponible'),
                    ]),

                Section::make('Componentes Principales')
                    ->description('Seleccione los componentes principales de la computadora (Obligatorios: Placa Base, CPU, Fuente de Poder, Gabinete, RAM y ROM)')
                    ->collapsible()
                    ->schema([
                        Select::make('motherboard_component_id')
                            ->label('Placa Base')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'Motherboard')
                                    ->where('status', 'Operativo')
                                    ->whereNull('output_date');
                                
                                if ($currentRecord) {
                                    // Si estamos editando, incluir componente actual O disponibles
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'Motherboard')
                                        ->first()?->id;
                                    
                                    if ($currentId) {
                                        $query->where(function ($q) use ($currentId) {
                                            $q->whereDoesntHave('computers')
                                                ->orWhere('id', $currentId);
                                        });
                                    } else {
                                        $query->whereDoesntHave('computers');
                                    }
                                } else {
                                    // Si estamos creando, solo mostrar disponibles
                                    $query->whereDoesntHave('computers');
                                }
                                
                                return $query->get()
                                    ->mapWithKeys(function ($component) {
                                        $mb = $component->componentable;
                                        return [$component->id => "{$mb->brand} {$mb->model} - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Limpiar selecciones dependientes
                                $set('cpu_component_id', null);
                                $set('rams', []);
                                $set('roms', []);
                            }),

                        Select::make('cpu_component_id')
                            ->label('Procesador (CPU)')
                            ->options(function (Get $get, $livewire) {
                                $motherboardComponentId = $get('motherboard_component_id');
                                if (!$motherboardComponentId) {
                                    return [];
                                }

                                $mbComponent = Component::find($motherboardComponentId);
                                if (!$mbComponent) {
                                    return [];
                                }

                                $motherboard = $mbComponent->componentable;
                                $socket = $motherboard->socket;

                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'CPU')
                                    ->where('status', 'Operativo')
                                    ->whereNull('output_date');
                                
                                if ($currentRecord) {
                                    // Si estamos editando, incluir componente actual O disponibles
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'CPU')
                                        ->first()?->id;
                                    
                                    if ($currentId) {
                                        $query->where(function ($q) use ($currentId) {
                                            $q->whereDoesntHave('computers')
                                                ->orWhere('id', $currentId);
                                        });
                                    } else {
                                        $query->whereDoesntHave('computers');
                                    }
                                } else {
                                    // Si estamos creando, solo mostrar disponibles
                                    $query->whereDoesntHave('computers');
                                }

                                return $query->get()
                                    ->filter(function ($component) use ($socket) {
                                        return $component->componentable->socket === $socket;
                                    })
                                    ->mapWithKeys(function ($component) {
                                        $cpu = $component->componentable;
                                        return [$component->id => "{$cpu->brand} {$cpu->model} ({$cpu->socket}) - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (Get $get) => !$get('motherboard_component_id'))
                            ->helperText(fn (Get $get) => $get('motherboard_component_id') 
                                ? 'Filtra CPUs compatibles con la placa base seleccionada' 
                                : 'Primero seleccione una placa base'),

                        Select::make('gpu_component_id')
                            ->label('Tarjeta Gráfica (GPU) - Opcional')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'GPU')
                                    ->where('status', 'Operativo')
                                    ->whereNull('output_date');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'GPU')
                                        ->first()?->id;
                                    
                                    if ($currentId) {
                                        $query->where(function ($q) use ($currentId) {
                                            $q->whereDoesntHave('computers')
                                                ->orWhere('id', $currentId);
                                        });
                                    } else {
                                        $query->whereDoesntHave('computers');
                                    }
                                } else {
                                    $query->whereDoesntHave('computers');
                                }
                                
                                return $query->get()
                                    ->mapWithKeys(function ($component) {
                                        $gpu = $component->componentable;
                                        return [$component->id => "{$gpu->brand} {$gpu->model} ({$gpu->memory}GB) - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable()
                            ->preload(),

                        Repeater::make('rams')
                            ->label('Memorias RAM')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Memoria RAM')
                                    ->options(function ($livewire) {
                                        $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                            ? $livewire->getRecord() 
                                            : null;
                                        
                                        $query = Component::where('componentable_type', 'RAM')
                                            ->where('status', 'Operativo')
                                            ->whereNull('output_date');
                                        
                                        if ($currentRecord) {
                                            // Si estamos editando, incluir componentes actuales O disponibles
                                            $currentIds = $currentRecord->components()
                                                ->where('components.componentable_type', 'RAM')
                                                ->pluck('components.id')
                                                ->toArray();
                                            
                                            $query->where(function ($q) use ($currentIds) {
                                                $q->whereDoesntHave('computers')
                                                    ->orWhereIn('id', $currentIds);
                                            });
                                        } else {
                                            // Si estamos creando, solo mostrar disponibles
                                            $query->whereDoesntHave('computers');
                                        }
                                        
                                        return $query->get()
                                            ->mapWithKeys(function ($component) {
                                                $ram = $component->componentable;
                                                return [$component->id => "{$ram->brand} {$ram->model} - {$ram->capacity}GB {$ram->type} - Serial: {$component->serial}"];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->required()
                            ->minItems(1)
                            ->maxItems(function (Get $get) {
                                $motherboardComponentId = $get('motherboard_component_id');
                                if (!$motherboardComponentId) {
                                    return 4;
                                }
                                $mbComponent = Component::find($motherboardComponentId);
                                return $mbComponent ? $mbComponent->componentable->slots_ram : 4;
                            })
                            ->defaultItems(1)
                            ->addActionLabel('Agregar RAM')
                            ->collapsible()
                            ->helperText(fn (Get $get) => $get('motherboard_component_id') 
                                ? 'Máximo de ranuras según placa base seleccionada' 
                                : 'Seleccione una placa base primero'),

                        Repeater::make('roms')
                            ->label('Unidades de Almacenamiento (ROM)')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Almacenamiento')
                                    ->options(function ($livewire) {
                                        $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                            ? $livewire->getRecord() 
                                            : null;
                                        
                                        $query = Component::where('componentable_type', 'ROM')
                                            ->where('status', 'Operativo')
                                            ->whereNull('output_date');
                                        
                                        if ($currentRecord) {
                                            // Si estamos editando, incluir componentes actuales O disponibles
                                            $currentIds = $currentRecord->components()
                                                ->where('components.componentable_type', 'ROM')
                                                ->pluck('components.id')
                                                ->toArray();
                                            
                                            $query->where(function ($q) use ($currentIds) {
                                                $q->whereDoesntHave('computers')
                                                    ->orWhereIn('id', $currentIds);
                                            });
                                        } else {
                                            // Si estamos creando, solo mostrar disponibles
                                            $query->whereDoesntHave('computers');
                                        }
                                        
                                        return $query->get()
                                            ->mapWithKeys(function ($component) {
                                                $rom = $component->componentable;
                                                return [$component->id => "{$rom->brand} {$rom->model} - {$rom->capacity}GB {$rom->type} - Serial: {$component->serial}"];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->required()
                            ->minItems(1)
                            ->maxItems(function (Get $get) {
                                $motherboardComponentId = $get('motherboard_component_id');
                                if (!$motherboardComponentId) {
                                    return 6;
                                }
                                $mbComponent = Component::find($motherboardComponentId);
                                if (!$mbComponent) {
                                    return 6;
                                }
                                $mb = $mbComponent->componentable;
                                return ($mb->ports_sata ?? 0) + ($mb->ports_m2 ?? 0);
                            })
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Almacenamiento')
                            ->collapsible()
                            ->helperText('Discos duros, SSDs, etc.'),

                        Select::make('power_supply_component_id')
                            ->label('Fuente de Poder')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'PowerSupply')
                                    ->where('status', 'Operativo')
                                    ->whereNull('output_date');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'PowerSupply')
                                        ->first()?->id;
                                    
                                    if ($currentId) {
                                        $query->where(function ($q) use ($currentId) {
                                            $q->whereDoesntHave('computers')
                                                ->orWhere('id', $currentId);
                                        });
                                    } else {
                                        $query->whereDoesntHave('computers');
                                    }
                                } else {
                                    $query->whereDoesntHave('computers');
                                }
                                
                                return $query->get()
                                    ->mapWithKeys(function ($component) {
                                        $ps = $component->componentable;
                                        return [$component->id => "{$ps->brand} {$ps->model} - {$ps->watts}W - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('tower_case_component_id')
                            ->label('Gabinete')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'TowerCase')
                                    ->where('status', 'Operativo')
                                    ->whereNull('output_date');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'TowerCase')
                                        ->first()?->id;
                                    
                                    if ($currentId) {
                                        $query->where(function ($q) use ($currentId) {
                                            $q->whereDoesntHave('computers')
                                                ->orWhere('id', $currentId);
                                        });
                                    } else {
                                        $query->whereDoesntHave('computers');
                                    }
                                } else {
                                    $query->whereDoesntHave('computers');
                                }
                                
                                return $query->get()
                                    ->mapWithKeys(function ($component) {
                                        $tc = $component->componentable;
                                        return [$component->id => "{$tc->brand} {$tc->model} - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('network_adapter_component_id')
                            ->label('Adaptador de Red - Opcional')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'NetworkAdapter')
                                    ->where('status', 'Operativo')
                                    ->whereNull('output_date');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'NetworkAdapter')
                                        ->first()?->id;
                                    
                                    if ($currentId) {
                                        $query->where(function ($q) use ($currentId) {
                                            $q->whereDoesntHave('computers')
                                                ->orWhere('id', $currentId);
                                        });
                                    } else {
                                        $query->whereDoesntHave('computers');
                                    }
                                } else {
                                    $query->whereDoesntHave('computers');
                                }
                                
                                return $query->get()
                                    ->mapWithKeys(function ($component) {
                                        $na = $component->componentable;
                                        return [$component->id => "{$na->brand} {$na->model} - {$na->speed} - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Periféricos')
                    ->description('Componentes externos opcionales (Si no llena nada, solo se registrará la CPU)')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('monitors')
                            ->label('Monitores')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Monitor')
                                    ->options(function () {
                                        return Component::where('componentable_type', 'Monitor')
                                            ->where('status', 'Operativo')
                                            ->whereDoesntHave('peripheral')
                                            ->get()
                                            ->mapWithKeys(function ($component) {
                                                $monitor = $component->componentable;
                                                return [$component->id => "{$monitor->brand} {$monitor->model} - {$monitor->screen_size}\" - Serial: {$component->serial}"];
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
                                ->options(function () {
                                    return Component::where('componentable_type', 'Keyboard')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $kb = $component->componentable;
                                            return [$component->id => "{$kb->brand} {$kb->model} - Serial: {$component->serial}"];
                                        });
                                })
                                ->searchable(),

                            Select::make('mouse_component_id')
                                ->label('Mouse')
                                ->options(function () {
                                    return Component::where('componentable_type', 'Mouse')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $mouse = $component->componentable;
                                            return [$component->id => "{$mouse->brand} {$mouse->model} - Serial: {$component->serial}"];
                                        });
                                })
                                ->searchable(),

                            Select::make('audio_component_id')
                                ->label('Dispositivo de Audio')
                                ->options(function () {
                                    return Component::where('componentable_type', 'AudioDevice')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $audio = $component->componentable;
                                            return [$component->id => "{$audio->brand} {$audio->model} ({$audio->type}) - Serial: {$component->serial}"];
                                        });
                                })
                                ->searchable(),

                            Select::make('stabilizer_component_id')
                                ->label('Estabilizador')
                                ->options(function () {
                                    return Component::where('componentable_type', 'Stabilizer')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $stab = $component->componentable;
                                            return [$component->id => "{$stab->brand} {$stab->model} - {$stab->capacity}VA - Serial: {$component->serial}"];
                                        });
                                })
                                ->searchable(),

                            Select::make('splitter_component_id')
                                ->label('Splitter')
                                ->options(function () {
                                    return Component::where('componentable_type', 'Splitter')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $splitter = $component->componentable;
                                            return [$component->id => "{$splitter->brand} {$splitter->model} - {$splitter->ports} puertos - Serial: {$component->serial}"];
                                        });
                                })
                                ->searchable(),
                        ]),
                    ]),
            ]);
    }
}
