<?php

namespace App\Filament\Resources\Peripherals\Tables;

use App\Models\Component;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeripheralsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Ubicación')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('computer.serial')
                    ->label('Asignado a CPU')
                    ->searchable()
                    ->default('Sin asignar')
                    ->badge()
                    ->color(fn ($state) => $state === 'Sin asignar' ? 'gray' : 'success'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Inactivo' => 'gray',
                        'Activo' => 'success',
                        'Desmantelado' => 'danger',
                    }),
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
                        'Activo' => 'Activo',
                        'Desmantelado' => 'Desmantelado',
                    ]),
                SelectFilter::make('location_id')
                    ->label('Ubicación')
                    ->relationship('location', 'name')
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('computer_id')
                    ->label('Estado de Asignación')
                    ->options([
                        'assigned' => 'Asignados',
                        'available' => 'Disponibles',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'assigned') {
                            return $query->whereNotNull('computer_id');
                        } elseif ($data['value'] === 'available') {
                            return $query->whereNull('computer_id');
                        }
                    }),
            ])
            ->recordActions([
                Action::make('verComponentes')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->modalHeading('Detalles del Periférico')
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->infolist(function ($record) {
                        $record->load(['components.componentable', 'location', 'computer']);
                        
                        $keyboard = $record->components->firstWhere('componentable_type', 'App\Models\Keyboard');
                        $mouse = $record->components->firstWhere('componentable_type', 'App\Models\Mouse');
                        $audioDevice = $record->components->firstWhere('componentable_type', 'App\Models\AudioDevice');
                        $stabilizer = $record->components->firstWhere('componentable_type', 'App\Models\Stabilizer');
                        $splitter = $record->components->firstWhere('componentable_type', 'App\Models\Splitter');
                        $monitors = $record->components->where('componentable_type', 'App\Models\Monitor');

                        return [
                            ViewEntry::make('info_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '📋 Información General',
                                    'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),
                            
                            Section::make()
                                ->schema([
                                    TextEntry::make('code')
                                        ->label('Código'),
                                    TextEntry::make('location.name')
                                        ->label('Ubicación'),
                                    TextEntry::make('computer.serial')
                                        ->label('Asignado a CPU')
                                        ->default('Sin asignar'),
                                    TextEntry::make('status')
                                        ->label('Estado')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'Inactivo' => 'gray',
                                            'Activo' => 'success',
                                            'Desmantelado' => 'danger',
                                        }),
                                    TextEntry::make('notes')
                                        ->label('Notas')
                                        ->default('Sin notas')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),

                            ViewEntry::make('components_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '🖥️ Componentes Periféricos',
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
                                                $num = $index + 1;
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid #10b981;'>" .
                                                       "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>Monitor #{$num}: {$m->brand} {$m->model}</div>" .
                                                       "<div style='line-height: 1.6;'>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Tamaño:</span> <span style='color: #9ca3af;'>{$m->screen_size} pulgadas</span><br>" .
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
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$kb->brand} {$kb->model}</div>" .
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
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$m->brand} {$m->model}</div>" .
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
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$ad->brand} {$ad->model}</div>" .
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
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$st->brand} {$st->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Potencia:</span> <span style='color: #9ca3af;'>{$st->power} VA</span></div>" .
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
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$sp->brand} {$sp->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Número de Tomas:</span> <span style='color: #9ca3af;'>{$sp->outlets}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$splitter->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$splitter->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(3),
                        ];
                    }),

                Action::make('actualizarComponentes')
                    ->label('Actualizar Componentes')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->modalHeading('Actualizar Componentes Periféricos')
                    ->modalDescription('Modifique los componentes del periférico')
                    ->modalWidth('6xl')
                    ->modalSubmitActionLabel('Guardar Cambios')
                    ->modalCancelActionLabel('Cancelar')
                    ->form([
                        Repeater::make('monitors')
                            ->label('Monitores')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Monitor')
                                    ->options(function ($record) {
                                        $currentMonitorIds = $record->components()
                                            ->where('components.componentable_type', 'App\Models\Monitor')
                                            ->pluck('components.id')
                                            ->toArray();
                                        
                                        $availableMonitors = Component::where('componentable_type', 'App\Models\Monitor')
                                            ->where('status', 'Operativo')
                                            ->where(function ($query) use ($currentMonitorIds) {
                                                $query->whereDoesntHave('peripherals')
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
                            ->collapsed(),

                        Grid::make(2)->schema([
                            Select::make('keyboard_component_id')
                                ->label('Teclado')
                                ->options(function ($record) {
                                    $current = $record->components->firstWhere('componentable_type', 'App\Models\Keyboard');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\Keyboard')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripherals')
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
                                    $current = $record->components->firstWhere('componentable_type', 'App\Models\Mouse');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\Mouse')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripherals')
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
                                    $current = $record->components->firstWhere('componentable_type', 'App\Models\AudioDevice');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\AudioDevice')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripherals')
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
                                    $current = $record->components->firstWhere('componentable_type', 'App\Models\Stabilizer');
                                    
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
                                    $current = $record->components->firstWhere('componentable_type', 'App\Models\Splitter');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\Splitter')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripherals')
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
                    ])
                    ->fillForm(function ($record): array {
                        return [
                            'keyboard_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\Keyboard')?->id,
                            'mouse_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\Mouse')?->id,
                            'audio_device_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\AudioDevice')?->id,
                            'stabilizer_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\Stabilizer')?->id,
                            'splitter_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\Splitter')?->id,
                            'monitors' => $record->components
                                ->where('componentable_type', 'App\Models\Monitor')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray(),
                        ];
                    })
                    ->action(function ($record, array $data): void {
                        $componentData = [
                            'keyboard' => $data['keyboard_component_id'] ?? null,
                            'mouse' => $data['mouse_component_id'] ?? null,
                            'audio_device' => $data['audio_device_component_id'] ?? null,
                            'stabilizer' => $data['stabilizer_component_id'] ?? null,
                            'splitter' => $data['splitter_component_id'] ?? null,
                            'monitors' => $data['monitors'] ?? [],
                        ];

                        $currentComponents = [
                            'keyboard' => $record->components->firstWhere('componentable_type', 'App\Models\Keyboard')?->id,
                            'mouse' => $record->components->firstWhere('componentable_type', 'App\Models\Mouse')?->id,
                            'audio_device' => $record->components->firstWhere('componentable_type', 'App\Models\AudioDevice')?->id,
                            'stabilizer' => $record->components->firstWhere('componentable_type', 'App\Models\Stabilizer')?->id,
                            'splitter' => $record->components->firstWhere('componentable_type', 'App\Models\Splitter')?->id,
                            'monitors' => $record->components->where('componentable_type', 'App\Models\Monitor')->pluck('id')->toArray(),
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
                            $record->components()->updateExistingPivot($componentsToRemove, [
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
                                $exists = $record->allComponents()->wherePivot('component_id', $componentId)->exists();
                                if ($exists) {
                                    $record->components()->updateExistingPivot($componentId, $pivotData);
                                } else {
                                    $record->components()->attach($componentId, $pivotData);
                                }
                            }
                        }

                        foreach ($componentData['monitors'] as $monitor) {
                            if (isset($monitor['component_id'])) {
                                $exists = $record->allComponents()->wherePivot('component_id', $monitor['component_id'])->exists();
                                if ($exists) {
                                    $record->components()->updateExistingPivot($monitor['component_id'], $pivotData);
                                } else {
                                    $record->components()->attach($monitor['component_id'], $pivotData);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Componentes actualizados')
                            ->success()
                            ->body('Todos los componentes periféricos han sido actualizados exitosamente.')
                            ->send();
                    }),

                Action::make('desmantelar')
                    ->label('Desmantelar')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Desmantelar Periférico')
                    ->modalDescription(fn ($record) => "¿Está seguro de desmantelar el periférico {$record->code}? Todos los componentes vigentes serán removidos y el periférico pasará al estado 'Desmantelado'.")
                    ->modalSubmitActionLabel('Sí, desmantelar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(fn ($record) => $record->status === 'Inactivo')
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            DB::table('componentables')
                                ->where('componentable_type', 'App\\Models\\Peripheral')
                                ->where('componentable_id', $record->id)
                                ->where('status', 'Vigente')
                                ->update([
                                    'status' => 'Removido',
                                    'updated_at' => now()
                                ]);
                            
                            $record->update(['status' => 'Desmantelado']);
                        });
                        
                        Notification::make()
                            ->title('Periférico desmantelado')
                            ->success()
                            ->body("El periférico {$record->code} ha sido desmantelado exitosamente.")
                            ->send();
                    }),

                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de periféricos');
    }
}
