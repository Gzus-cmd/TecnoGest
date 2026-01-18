<?php

namespace App\Filament\Resources\ComponentHistories;

use App\Filament\Resources\ComponentHistories\Pages\ManageComponentHistories;
use App\Models\Component;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use UnitEnum;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class ComponentHistoryResource extends Resource
{

    protected static ?string $model = Component::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Historial de Componentes';

    protected static ?string $modelLabel = 'Historial de Componente';

    protected static ?string $pluralModelLabel = 'Historial de Componentes';

    protected static string | UnitEnum | null $navigationGroup = 'Registros';

    protected static ?int $navigationSort = 71;

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Join con la tabla pivot para obtener el historial
                $query->join('componentables', 'components.id', '=', 'componentables.component_id')
                    ->with([
                        'componentable',
                        'computers.location',
                        'printers.location',
                        'projectors.location'
                    ])
                    ->select([
                        'components.id',
                        'components.serial',
                        'components.componentable_type',
                        'components.componentable_id',
                        'components.status',
                        'componentables.componentable_type as device_type',
                        'componentables.componentable_id as device_id',
                        'componentables.assigned_at',
                        'componentables.status as assignment_status',
                    ])
                    ->orderBy('componentables.assigned_at', 'desc');
            })
            ->columns([
                TextColumn::make('componentable_type')
                    ->label('Tipo / Serial')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'Motherboard' => 'Placa Base',
                        'CPU' => 'Procesador',
                        'GPU' => 'Tarjeta Gráfica',
                        'RAM' => 'Memoria RAM',
                        'ROM' => 'Almacenamiento',
                        'Monitor' => 'Monitor',
                        'Keyboard' => 'Teclado',
                        'Mouse' => 'Mouse',
                        'NetworkAdapter' => 'Adaptador de Red',
                        'PowerSupply' => 'Fuente de Poder',
                        'TowerCase' => 'Gabinete',
                        'AudioDevice' => 'Dispositivo de Audio',
                        'Stabilizer' => 'Estabilizador',
                        'Splitter' => 'Splitter',
                        'SparePart' => 'Repuesto',
                        default => 'Otro',
                    })
                    ->description(fn($record) => "🔢 " . ($record->serial ?? 'N/A'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->where('components.componentable_type', 'like', "%{$search}%")
                                ->orWhere('components.serial', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('componentable.brand')
                    ->label('Marca / Modelo')
                    ->getStateUsing(function ($record) {
                        $type = $record->componentable_type;
                        $id = $record->componentable_id;
                        if (!$type || !$id) return 'N/A';

                        try {
                            // Asegurar que el tipo tenga el namespace completo
                            if (!str_starts_with($type, 'App\\Models\\')) {
                                $type = 'App\\Models\\' . $type;
                            }

                            if (!class_exists($type)) return 'N/A';

                            $component = $type::find($id);
                            if (!$component) return 'N/A';

                            return $component->brand ?? 'N/A';
                        } catch (\Exception $e) {
                            return 'N/A';
                        }
                    })
                    ->description(function ($record) {
                        $type = $record->componentable_type;
                        $id = $record->componentable_id;
                        if (!$type || !$id) return '';

                        try {
                            // Asegurar que el tipo tenga el namespace completo
                            if (!str_starts_with($type, 'App\\Models\\')) {
                                $type = 'App\\Models\\' . $type;
                            }

                            if (!class_exists($type)) return '';

                            $component = $type::find($id);
                            if (!$component) return '';

                            return "🏷️ " . ($component->model ?? 'N/A');
                        } catch (\Exception $e) {
                            return '';
                        }
                    })
                    ->wrap(),

                TextColumn::make('device_info')
                    ->label('Asignado a')
                    ->getStateUsing(function ($record) {
                        $deviceType = $record->device_type ?? null;
                        $deviceId = $record->device_id ?? null;

                        if (!$deviceType || !$deviceId) return 'N/A';

                        try {
                            // Buscar el dispositivo
                            if (str_contains($deviceType, 'Computer')) {
                                $device = Computer::find($deviceId);
                                $type = 'PC';
                            } elseif (str_contains($deviceType, 'Printer')) {
                                $device = Printer::find($deviceId);
                                $type = 'Impresora';
                            } elseif (str_contains($deviceType, 'Projector')) {
                                $device = Projector::find($deviceId);
                                $type = 'Proyector';
                            } else {
                                return 'Dispositivo Desconocido';
                            }

                            if (!$device) return 'Dispositivo no encontrado';

                            $location = $device->location->name ?? 'Sin ubicación';
                            return "{$type}: {$device->serial} ({$location})";
                        } catch (\Exception $e) {
                            return 'Error al cargar dispositivo';
                        }
                    }),

                TextColumn::make('assigned_at')
                    ->label('Fecha de Asignación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('assignment_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Vigente' => 'success',
                        'Removido' => 'warning',
                        'Desmantelado' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('componentable_type')
                    ->label('Tipo de Componente')
                    ->options([
                        'Motherboard' => 'Placa Base',
                        'CPU' => 'Procesador',
                        'GPU' => 'Tarjeta Gráfica',
                        'RAM' => 'Memoria RAM',
                        'ROM' => 'Almacenamiento',
                        'Monitor' => 'Monitor',
                        'Keyboard' => 'Teclado',
                        'Mouse' => 'Mouse',
                        'NetworkAdapter' => 'Adaptador de Red',
                        'PowerSupply' => 'Fuente de Poder',
                        'TowerCase' => 'Gabinete',
                        'AudioDevice' => 'Dispositivo de Audio',
                        'Stabilizer' => 'Estabilizador',
                        'Splitter' => 'Splitter',
                        'SparePart' => 'Repuesto',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereIn('components.componentable_type', $data['values']);
                        } elseif (!empty($data['value'])) {
                            $query->where('components.componentable_type', $data['value']);
                        }
                    })
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('assignment_status')
                    ->label('Estado de Asignación')
                    ->options([
                        'Vigente' => 'Vigente',
                        'Removido' => 'Removido',
                        'Desmantelado' => 'Desmantelado',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value']) && $data['value']) {
                            $query->where('componentables.status', $data['value']);
                        }
                    })
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('device_type')
                    ->label('Tipo de Dispositivo')
                    ->options([
                        'Computer' => 'Computadora',
                        'Printer' => 'Impresora',
                        'Projector' => 'Proyector',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value']) && $data['value']) {
                            $query->where('componentables.componentable_type', 'like', '%' . $data['value'] . '%');
                        }
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('device_id')
                    ->label('Dispositivo Específico')
                    ->options(function () {
                        $devices = [];

                        // Obtener todas las computadoras
                        $computers = Computer::with('location')->get();
                        foreach ($computers as $computer) {
                            $location = $computer->location->name ?? 'Sin ubicación';
                            $devices["Computer-{$computer->id}"] = "PC: {$computer->serial} ({$location})";
                        }

                        // Obtener todas las impresoras
                        $printers = Printer::with('location')->get();
                        foreach ($printers as $printer) {
                            $location = $printer->location->name ?? 'Sin ubicación';
                            $devices["Printer-{$printer->id}"] = "Impresora: {$printer->serial} ({$location})";
                        }

                        // Obtener todos los proyectores
                        $projectors = Projector::with('location')->get();
                        foreach ($projectors as $projector) {
                            $location = $projector->location->name ?? 'Sin ubicación';
                            $devices["Projector-{$projector->id}"] = "Proyector: {$projector->serial} ({$location})";
                        }

                        return $devices;
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value']) && $data['value']) {
                            // Parsear el valor "Computer-5" -> tipo: Computer, id: 5
                            $parts = explode('-', $data['value']);
                            if (count($parts) === 2) {
                                $type = $parts[0];
                                $id = $parts[1];
                                $query->where('componentables.componentable_type', 'like', "%{$type}%")
                                    ->where('componentables.componentable_id', $id);
                            }
                        }
                    })
                    ->searchable()
                    ->preload(),

                Filter::make('assigned_at')
                    ->form([
                        DatePicker::make('assigned_from')
                            ->label('Asignado desde'),
                        DatePicker::make('assigned_until')
                            ->label('Asignado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['assigned_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('componentables.assigned_at', '>=', $date),
                            )
                            ->when(
                                $data['assigned_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('componentables.assigned_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['assigned_from'] ?? null) {
                            $indicators[] = 'Asignado desde ' . \Carbon\Carbon::parse($data['assigned_from'])->format('d/m/Y');
                        }
                        if ($data['assigned_until'] ?? null) {
                            $indicators[] = 'Asignado hasta ' . \Carbon\Carbon::parse($data['assigned_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Filter::make('removed_at')
                    ->form([
                        DatePicker::make('removed_from')
                            ->label('Salida desde'),
                        DatePicker::make('removed_until')
                            ->label('Salida hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['removed_from'] || $data['removed_until'],
                                fn(Builder $query): Builder => $query->where('componentables.status', '!=', 'Vigente'),
                            )
                            ->when(
                                $data['removed_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('componentables.updated_at', '>=', $date),
                            )
                            ->when(
                                $data['removed_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('componentables.updated_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['removed_from'] ?? null) {
                            $indicators[] = 'Salida desde ' . \Carbon\Carbon::parse($data['removed_from'])->format('d/m/Y');
                        }
                        if ($data['removed_until'] ?? null) {
                            $indicators[] = 'Salida hasta ' . \Carbon\Carbon::parse($data['removed_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->defaultSort('assigned_at', 'desc')
            ->recordActions([])
            ->toolbarActions([
                \Filament\Actions\Action::make('exportExcel')
                    ->visible(
                        fn() =>
                        \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin' &&
                            Auth::user()?->can('ComponentHistoryExport')
                    )
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filename = 'historial_componentes_' . now()->format('Y-m-d_His') . '.xlsx';
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ComponentHistoryExport($records),
                            $filename,
                            \Maatwebsite\Excel\Excel::XLSX
                        );
                    }),

                \Filament\Actions\Action::make('exportCsv')
                    ->visible(
                        fn() =>
                        \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin' &&
                            Auth::user()?->can('ComponentHistoryExport')
                    )
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filename = 'historial_componentes_' . now()->format('Y-m-d_His') . '.csv';
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ComponentHistoryExport($records),
                            $filename,
                            \Maatwebsite\Excel\Excel::CSV
                        );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageComponentHistories::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
