<?php

namespace App\Filament\Resources\Peripherals\Schemas;

use App\Models\Component;
use App\Models\Location;
use App\Models\Computer;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PeripheralForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información del Periférico')
                    ->description('Datos básicos del conjunto de periféricos')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Código')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50)
                                ->default(fn () => 'PER-' . str_pad((\App\Models\Peripheral::max('id') ?? 0) + 1, 3, '0', STR_PAD_LEFT))
                                ->helperText('Código único para identificar este conjunto de periféricos'),
                            
                            Select::make('location_id')
                                ->label('Ubicación')
                                ->options(Location::all()->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->helperText('Departamento donde se encuentra el periférico'),
                            
                            Select::make('computer_id')
                                ->label('Asignado a CPU')
                                ->options(function ($record) {
                                    $query = Computer::query();
                                    
                                    // Si estamos editando, incluir la computadora actual O disponibles
                                    if ($record && $record->computer_id) {
                                        $query->where(function ($q) use ($record) {
                                            $q->whereNull('peripheral_id')
                                              ->orWhere('id', $record->computer_id);
                                        });
                                    } else {
                                        // Si estamos creando, solo disponibles
                                        $query->whereNull('peripheral_id');
                                    }
                                    
                                    return $query->with('location')->get()->mapWithKeys(function ($computer) use ($record) {
                                        $location = $computer->location ? " - {$computer->location->name}" : '';
                                        return [$computer->id => "{$computer->serial}{$location}"];
                                    });
                                })
                                ->searchable()
                                ->nullable()
                                ->placeholder('Sin asignar')
                                ->helperText('Seleccione una computadora disponible'),
                        ]),
                        
                        Textarea::make('notes')
                            ->label('Notas')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Componentes Periféricos')
                    ->description('Monitores, teclado, mouse y otros periféricos')
                    ->schema([
                        Repeater::make('monitors')
                            ->label('Monitores')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Monitor')
                                    ->options(function ($record) {
                                        // Obtener IDs de componentes Monitor actualmente asignados a este periférico
                                        $currentMonitorIds = $record 
                                            ? $record->components()
                                                ->where('components.componentable_type', 'App\Models\Monitor')
                                                ->pluck('components.id')
                                                ->toArray()
                                            : [];
                                        
                                        // Obtener todos los componentes Monitor operativos que:
                                        // 1. No están asignados a ningún peripheral
                                        // 2. O están asignados a ESTE peripheral
                                        $availableMonitors = Component::where('componentable_type', 'App\Models\Monitor')
                                            ->where('status', 'Operativo')
                                            ->where(function ($query) use ($currentMonitorIds) {
                                                $query->whereDoesntHave('peripheral')
                                                    ->orWhereIn('id', $currentMonitorIds);
                                            })
                                            ->get();
                                        
                                        return $availableMonitors->mapWithKeys(function ($component) use ($currentMonitorIds) {
                                            $monitor = $component->componentable;
                                            $label = "{$monitor->brand} {$monitor->model} - {$monitor->screen_size}\" - Serial: {$component->serial}";
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
                                    $current = $record?->components->firstWhere('componentable_type', 'App\Models\Keyboard');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\Keyboard')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $kb = $component->componentable;
                                            return [$component->id => "{$kb->brand} {$kb->model} - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $kb = $current->componentable;
                                        $available->prepend("{$kb->brand} {$kb->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }
                                    
                                    return $available;
                                })
                                ->searchable(),

                            Select::make('mouse_component_id')
                                ->label('Mouse')
                                ->options(function ($record) {
                                    $current = $record?->components->firstWhere('componentable_type', 'App\Models\Mouse');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\Mouse')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $mouse = $component->componentable;
                                            return [$component->id => "{$mouse->brand} {$mouse->model} - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $mouse = $current->componentable;
                                        $available->prepend("{$mouse->brand} {$mouse->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->searchable(),

                            Select::make('audio_device_component_id')
                                ->label('Dispositivo de Audio')
                                ->options(function ($record) {
                                    $current = $record?->components->firstWhere('componentable_type', 'App\Models\AudioDevice');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\AudioDevice')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $audio = $component->componentable;
                                            return [$component->id => "{$audio->brand} {$audio->model} - Serial: {$component->serial}"];
                                        });
                                        
                                    if ($current) {
                                        $audio = $current->componentable;
                                        $available->prepend("{$audio->brand} {$audio->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }
                                    
                                    return $available;
                                })
                                ->searchable(),

                            Select::make('stabilizer_component_id')
                                ->label('Estabilizador')
                                ->options(function ($record) {
                                    $current = $record?->components->firstWhere('componentable_type', 'App\Models\Stabilizer');
                                    
                                    // Los estabilizadores pueden estar asignados a múltiples dispositivos
                                    $available = Component::where('componentable_type', 'App\Models\Stabilizer')
                                        ->where('status', 'Operativo')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $stab = $component->componentable;
                                            return [$component->id => "{$stab->brand} {$stab->model} - {$stab->power}VA - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $stab = $current->componentable;
                                        $available->prepend("{$stab->brand} {$stab->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->searchable(),

                            Select::make('splitter_component_id')
                                ->label('Multicontacto/Splitter')
                                ->options(function ($record) {
                                    $current = $record?->components->firstWhere('componentable_type', 'App\Models\Splitter');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\Splitter')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $split = $component->componentable;
                                            return [$component->id => "{$split->brand} {$split->model} - {$split->outlets} tomas - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $split = $current->componentable;
                                        $available->prepend("{$split->brand} {$split->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->searchable(),
                        ]),
                    ]),
            ]);
    }
}
