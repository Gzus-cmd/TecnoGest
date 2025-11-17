<?php

namespace App\Filament\Resources\ComponentHistories;

use App\Filament\Resources\ComponentHistories\Pages\ManageComponentHistories;
use App\Models\Component;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
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
                    ->label('Tipo de Componente')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\Motherboard' => 'Placa Base',
                        'App\Models\CPU' => 'Procesador',
                        'App\Models\GPU' => 'Tarjeta Gráfica',
                        'App\Models\RAM' => 'Memoria RAM',
                        'App\Models\ROM' => 'Almacenamiento',
                        'App\Models\Monitor' => 'Monitor',
                        'App\Models\Keyboard' => 'Teclado',
                        'App\Models\Mouse' => 'Mouse',
                        'App\Models\NetworkAdapter' => 'Adaptador de Red',
                        'App\Models\PowerSupply' => 'Fuente de Poder',
                        'App\Models\TowerCase' => 'Gabinete',
                        'App\Models\AudioDevice' => 'Dispositivo de Audio',
                        'App\Models\Stabilizer' => 'Estabilizador',
                        'App\Models\Splitter' => 'Splitter',
                        'App\Models\SparePart' => 'Repuesto',
                        default => 'Otro',
                    })
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('component_details')
                    ->label('Componente')
                    ->getStateUsing(function ($record) {
                        $type = $record->componentable_type;
                        $id = $record->componentable_id;
                        if (!$type || !$id) return 'N/A';
                        
                        try {
                            $component = $type::find($id);
                            if (!$component) return 'N/A';
                            
                            $brand = $component->brand ?? 'N/A';
                            $model = $component->model ?? 'N/A';
                            
                            return "{$brand} {$model} - {$record->serial}";
                        } catch (\Exception $e) {
                            return 'N/A';
                        }
                    })
                    ->searchable(),
                    
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
                    })
                    ->searchable(),
                    
                TextColumn::make('assigned_at')
                    ->label('Fecha de Asignación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('assignment_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
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
                        'App\Models\Motherboard' => 'Placa Base',
                        'App\Models\CPU' => 'Procesador',
                        'App\Models\GPU' => 'Tarjeta Gráfica',
                        'App\Models\RAM' => 'Memoria RAM',
                        'App\Models\ROM' => 'Almacenamiento',
                        'App\Models\Monitor' => 'Monitor',
                        'App\Models\Keyboard' => 'Teclado',
                        'App\Models\Mouse' => 'Mouse',
                        'App\Models\NetworkAdapter' => 'Adaptador de Red',
                        'App\Models\PowerSupply' => 'Fuente de Poder',
                        'App\Models\TowerCase' => 'Gabinete',
                        'App\Models\AudioDevice' => 'Dispositivo de Audio',
                        'App\Models\Stabilizer' => 'Estabilizador',
                        'App\Models\Splitter' => 'Splitter',
                        'App\Models\SparePart' => 'Repuesto',
                    ])
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
                                fn (Builder $query, $date): Builder => $query->whereDate('componentables.assigned_at', '>=', $date),
                            )
                            ->when(
                                $data['assigned_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('componentables.assigned_at', '<=', $date),
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
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->defaultSort('assigned_at', 'desc')
            ->recordActions([])
            ->toolbarActions([
                \Filament\Actions\Action::make('exportExcel')
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
