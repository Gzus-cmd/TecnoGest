<?php

namespace App\Filament\Resources\Computers\Schemas;

use App\Models\Component;
use App\Models\CPU;
use App\Models\GPU;
use App\Models\Location;
use App\Models\Motherboard;
use App\Models\RAM;
use App\Models\ROM;
use App\Models\Monitor;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\NetworkAdapter;
use App\Models\PowerSupply;
use App\Models\TowerCase;
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
                                
                                $query = Component::where('componentable_type', 'App\Models\Motherboard')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    // Si estamos editando, incluir componente actual O disponibles
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\Motherboard')
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
                                
                                $query = Component::where('componentable_type', 'App\Models\CPU')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    // Si estamos editando, incluir componente actual O disponibles
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\CPU')
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
                                
                                $query = Component::where('componentable_type', 'App\Models\GPU')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\GPU')
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
                            ->searchable(),

                        Repeater::make('rams')
                            ->label('Memorias RAM')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Memoria RAM')
                                    ->options(function ($livewire) {
                                        $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                            ? $livewire->getRecord() 
                                            : null;
                                        
                                        $query = Component::where('componentable_type', 'App\Models\RAM')
                                            ->where('status', 'Operativo');
                                        
                                        if ($currentRecord) {
                                            // Si estamos editando, incluir componentes actuales O disponibles
                                            $currentIds = $currentRecord->components()
                                                ->where('components.componentable_type', 'App\Models\RAM')
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
                                        
                                        $query = Component::where('componentable_type', 'App\Models\ROM')
                                            ->where('status', 'Operativo');
                                        
                                        if ($currentRecord) {
                                            // Si estamos editando, incluir componentes actuales O disponibles
                                            $currentIds = $currentRecord->components()
                                                ->where('components.componentable_type', 'App\Models\ROM')
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
                                
                                $query = Component::where('componentable_type', 'App\Models\PowerSupply')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\PowerSupply')
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
                                        return [$component->id => "{$ps->brand} {$ps->model} - {$ps->power}W - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable()
                            ->required(),

                        Select::make('tower_case_component_id')
                            ->label('Gabinete')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'App\Models\TowerCase')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\TowerCase')
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
                            ->required(),

                        Select::make('network_adapter_component_id')
                            ->label('Adaptador de Red - Opcional')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'App\Models\NetworkAdapter')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\NetworkAdapter')
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
                            ->searchable(),
                    ]),

                Section::make('Periféricos')
                    ->description('Periféricos asociados a la computadora')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Select::make('stabilizer_component_id')
                            ->label('Estabilizador')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'App\Models\Stabilizer')
                                    ->where('status', 'Operativo');
                                
                                // Los estabilizadores pueden estar asignados a múltiples dispositivos
                                // No aplicamos whereDoesntHave para permitir reutilización
                                
                                return $query->get()
                                    ->mapWithKeys(function ($component) {
                                        $stab = $component->componentable;
                                        return [$component->id => "{$stab->brand} {$stab->model} - {$stab->capacity}VA - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable(),

                        Repeater::make('monitors')
                            ->label('Monitores')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Monitor')
                                    ->options(function ($livewire) {
                                        $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                            ? $livewire->getRecord() 
                                            : null;
                                        
                                        $query = Component::where('componentable_type', 'App\Models\Monitor')
                                            ->where('status', 'Operativo');
                                        
                                        if ($currentRecord) {
                                            // Si estamos editando, incluir componentes actuales O disponibles
                                            $currentIds = $currentRecord->components()
                                                ->where('components.componentable_type', 'App\Models\Monitor')
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
                                                $monitor = $component->componentable;
                                                return [$component->id => "{$monitor->brand} {$monitor->model} - {$monitor->size}\" - Serial: {$component->serial}"];
                                            });
                                    })
                                    ->searchable()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->minItems(0)
                            ->maxItems(4)
                            ->addActionLabel('Agregar Monitor')
                            ->collapsible(),

                        Select::make('keyboard_component_id')
                            ->label('Teclado - Opcional')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'App\Models\Keyboard')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\Keyboard')
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
                                        $kb = $component->componentable;
                                        return [$component->id => "{$kb->brand} {$kb->model} - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable(),

                        Select::make('mouse_component_id')
                            ->label('Ratón - Opcional')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'App\Models\Mouse')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\Mouse')
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
                                        $mouse = $component->componentable;
                                        return [$component->id => "{$mouse->brand} {$mouse->model} - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable(),

                        Select::make('audio_device_component_id')
                            ->label('Dispositivo de Audio - Opcional')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'App\Models\AudioDevice')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\AudioDevice')
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
                                        $ad = $component->componentable;
                                        return [$component->id => "{$ad->brand} {$ad->model} - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable(),

                        Select::make('splitter_component_id')
                            ->label('Splitter - Opcional')
                            ->options(function ($livewire) {
                                $currentRecord = $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? $livewire->getRecord() 
                                    : null;
                                
                                $query = Component::where('componentable_type', 'App\Models\Splitter')
                                    ->where('status', 'Operativo');
                                
                                if ($currentRecord) {
                                    $currentId = $currentRecord->components()
                                        ->where('components.componentable_type', 'App\Models\Splitter')
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
                                        $sp = $component->componentable;
                                        return [$component->id => "{$sp->brand} {$sp->model} - {$sp->ports} puertos - Serial: {$component->serial}"];
                                    });
                            })
                            ->searchable(),
                    ]),
            ]);
    }
}
