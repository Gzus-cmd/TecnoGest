<?php

namespace App\Filament\Resources\Peripherals\Tables;

use App\Models\Component;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                    ->color(fn($state) => $state === 'Sin asignar' ? 'gray' : 'success'),
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
                    ->visible(fn() => auth()->user()?->can('PeripheralViewComponents'))
                    ->modalHeading('Detalles del Periférico')
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->infolist(function ($record) {
                        $record->load(['components.componentable', 'location', 'computer']);

                        $keyboard = $record->components->firstWhere('componentable_type', 'Keyboard');
                        $mouse = $record->components->firstWhere('componentable_type', 'Mouse');
                        $audioDevice = $record->components->firstWhere('componentable_type', 'AudioDevice');
                        $stabilizer = $record->components->firstWhere('componentable_type', 'Stabilizer');
                        $splitter = $record->components->firstWhere('componentable_type', 'Splitter');
                        $monitors = $record->components->where('componentable_type', 'Monitor');

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
                                                $brand = $m->brand ?? 'N/A';
                                                $model = $m->model ?? 'N/A';
                                                $screenSize = $m->screen_size ?? 'N/A';
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

                Action::make('actualizarComponentes')
                    ->label('Actualizar')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->visible(fn() => auth()->user()?->can('PeripheralUpdateComponents'))
                    ->modalHeading('Actualizar')
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
                                            ->where('components.componentable_type', 'Monitor')
                                            ->pluck('components.id')
                                            ->toArray();

                                        $availableMonitors = Component::where('componentable_type', 'Monitor')
                                            ->where('status', 'Operativo')
                                            ->where(function ($query) use ($currentMonitorIds) {
                                                $query->whereDoesntHave('peripheral')
                                                    ->orWhereIn('id', $currentMonitorIds);
                                            })
                                            ->get();

                                        return $availableMonitors->mapWithKeys(function ($component) use ($currentMonitorIds) {
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
                            ->collapsed(),

                        Grid::make(2)->schema([
                            Select::make('keyboard_component_id')
                                ->label('Teclado')
                                ->options(function ($record) {
                                    $current = $record->components->firstWhere('componentable_type', 'Keyboard');

                                    $available = Component::where('componentable_type', 'Keyboard')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
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
                                    $current = $record->components->firstWhere('componentable_type', 'Mouse');

                                    $available = Component::where('componentable_type', 'Mouse')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
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

                            Select::make('audio_device_component_id')
                                ->label('Dispositivo de Audio')
                                ->options(function ($record) {
                                    $current = $record->components->firstWhere('componentable_type', 'AudioDevice');

                                    $available = Component::where('componentable_type', 'AudioDevice')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $audio = $component->componentable;
                                            $brand = $audio->brand ?? 'N/A';
                                            $model = $audio->model ?? 'N/A';
                                            return [$component->id => "{$brand} {$model} - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $audio = $current->componentable;
                                        $brand = $audio->brand ?? 'N/A';
                                        $model = $audio->model ?? 'N/A';
                                        $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->searchable(),

                            Select::make('stabilizer_component_id')
                                ->label('Estabilizador')
                                ->options(function ($record) {
                                    $current = $record->components->firstWhere('componentable_type', 'Stabilizer');

                                    $available = Component::where('componentable_type', 'Stabilizer')
                                        ->where('status', 'Operativo')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $stab = $component->componentable;
                                            $brand = $stab->brand ?? 'N/A';
                                            $model = $stab->model ?? 'N/A';
                                            $power = $stab->power ?? 'N/A';
                                            return [$component->id => "{$brand} {$model} - {$power}VA - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $stab = $current->componentable;
                                        $brand = $stab->brand ?? 'N/A';
                                        $model = $stab->model ?? 'N/A';
                                        $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->searchable(),

                            Select::make('splitter_component_id')
                                ->label('Multicontacto/Splitter')
                                ->options(function ($record) {
                                    $current = $record->components->firstWhere('componentable_type', 'Splitter');

                                    $available = Component::where('componentable_type', 'Splitter')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('peripheral')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $split = $component->componentable;
                                            $brand = $split->brand ?? 'N/A';
                                            $model = $split->model ?? 'N/A';
                                            $outlets = $split->outlets ?? 'N/A';
                                            return [$component->id => "{$brand} {$model} - {$outlets} tomas - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $split = $current->componentable;
                                        $brand = $split->brand ?? 'N/A';
                                        $model = $split->model ?? 'N/A';
                                        $available->prepend("{$brand} {$model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->searchable(),
                        ]),
                    ])
                    ->fillForm(function ($record): array {
                        return [
                            'keyboard_component_id' => $record->components->firstWhere('componentable_type', 'Keyboard')?->id,
                            'mouse_component_id' => $record->components->firstWhere('componentable_type', 'Mouse')?->id,
                            'audio_device_component_id' => $record->components->firstWhere('componentable_type', 'AudioDevice')?->id,
                            'stabilizer_component_id' => $record->components->firstWhere('componentable_type', 'Stabilizer')?->id,
                            'splitter_component_id' => $record->components->firstWhere('componentable_type', 'Splitter')?->id,
                            'monitors' => $record->components
                                ->where('componentable_type', 'Monitor')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray(),
                        ];
                    })
                    ->action(function ($record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $componentData = [
                                'keyboard' => $data['keyboard_component_id'] ?? null,
                                'mouse' => $data['mouse_component_id'] ?? null,
                                'audio_device' => $data['audio_device_component_id'] ?? null,
                                'stabilizer' => $data['stabilizer_component_id'] ?? null,
                                'splitter' => $data['splitter_component_id'] ?? null,
                                'monitors' => $data['monitors'] ?? [],
                            ];

                            $currentComponents = [
                                'keyboard' => $record->components->firstWhere('componentable_type', 'Keyboard')?->id,
                                'mouse' => $record->components->firstWhere('componentable_type', 'Mouse')?->id,
                                'audio_device' => $record->components->firstWhere('componentable_type', 'AudioDevice')?->id,
                                'stabilizer' => $record->components->firstWhere('componentable_type', 'Stabilizer')?->id,
                                'splitter' => $record->components->firstWhere('componentable_type', 'Splitter')?->id,
                                'monitors' => $record->components->where('componentable_type', 'Monitor')->pluck('id')->toArray(),
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
                        }); // Fin de DB::transaction

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
                    ->modalDescription(fn($record) => "¿Está seguro de desmantelar el periférico {$record->code}? Todos los componentes vigentes serán removidos.")
                    ->modalSubmitActionLabel('Sí, desmantelar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(
                        fn($record) =>
                        auth()->user()?->can('PeripheralDismantle') &&
                            $record->computer_id === null
                    )
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
                        });

                        Notification::make()
                            ->title('Periférico desmantelado')
                            ->success()
                            ->body("El periférico {$record->code} ha sido desmantelado exitosamente.")
                            ->send();
                    }),

                ActionGroup::make([
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
            ->emptyStateHeading('No hay ningún registro de periféricos');
    }
}
