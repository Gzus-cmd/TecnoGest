<?php

namespace App\Filament\Resources\Components\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Actions\ExportAction as TablesExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('componentable_type')
                    ->label('Tipo / Serial')
                    ->formatStateUsing(fn ($state) => match (true) {
                        str_contains($state, 'CPU') => 'Procesador',
                        str_contains($state, 'GPU') => 'Tarjeta Gráfica',
                        str_contains($state, 'RAM') => 'Memoria RAM',
                        str_contains($state, 'ROM') => 'Almacenamiento',
                        str_contains($state, 'PowerSupply') => 'Fuente de Poder',
                        str_contains($state, 'NetworkAdapter') => 'Adaptador de Red',
                        str_contains($state, 'Motherboard') => 'Placa Base',
                        str_contains($state, 'Monitor') => 'Monitor',
                        str_contains($state, 'Keyboard') => 'Teclado',
                        str_contains($state, 'Mouse') => 'Ratón',
                        str_contains($state, 'Stabilizer') => 'Estabilizador',
                        str_contains($state, 'TowerCase') => 'Gabinete',
                        str_contains($state, 'Splitter') => 'Splitter',
                        str_contains($state, 'AudioDevice') => 'Audio',
                        str_contains($state, 'SparePart') => 'Repuesto',
                        default => class_basename($state),
                    })
                    ->description(fn ($record) => "🔢 " . ($record->serial ?? 'N/A'))
                    ->searchable(query: function ($query, string $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('serial', 'like', "%{$search}%")
                              ->orWhere('componentable_type', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('componentable.brand')
                    ->label('Marca / Modelo')
                    ->getStateUsing(function ($record) {
                        $componentable = $record->componentable;
                        return $componentable?->brand ?? 'N/A';
                    })
                    ->description(function ($record) {
                        $componentable = $record->componentable;
                        return "🏷️ " . ($componentable?->model ?? 'N/A');
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Deficiente' => 'warning',
                        'Operativo' => 'success',
                        'Retirado' => 'danger',
                    }),
                TextColumn::make('current_assignment')
                    ->label('Asignado a')
                    ->getStateUsing(function ($record) {
                        if ($record->status === 'Retirado') {
                            return null;
                        }
                        
                        // Usar los conteos precargados
                        if ($record->computers_count > 0) {
                            return "PC ({$record->computers_count})";
                        }
                        
                        if ($record->printers_count > 0) {
                            return "Impresora ({$record->printers_count})";
                        }
                        
                        if ($record->projectors_count > 0) {
                            return "Proyector ({$record->projectors_count})";
                        }
                        
                        return 'Disponible';
                    })
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'Disponible' ? 'success' : ($state === null ? 'gray' : 'info'))
                    ->placeholder('—')
                    ->searchable(false),
                TextColumn::make('provider.name')
                    ->label('Proveedor')
                    ->searchable(),
                TextColumn::make('warranty_months')
                    ->label('Garantía (meses)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('input_date')
                    ->label('Entrada / Salida')
                    ->date('d/m/Y')
                    ->description(fn ($record) => $record->output_date ? "📤 " . $record->output_date->format('d/m/Y') : '📤 —')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('componentable_type')
                    ->options([
                        'App\Models\CPU' => 'Procesador',
                        'App\Models\GPU' => 'Tarjeta Gráfica',
                        'App\Models\RAM' => 'Memoria RAM',
                        'App\Models\ROM' => 'Almacenamiento',
                        'App\Models\PowerSupply' => 'Fuente de Poder',
                        'App\Models\NetworkAdapter' => 'Adaptador de Red',
                        'App\Models\Motherboard' => 'Placa Base',
                        'App\Models\Monitor' => 'Monitor',
                        'App\Models\Keyboard' => 'Teclado',
                        'App\Models\Mouse' => 'Ratón',
                        'App\Models\Stabilizer' => 'Estabilizador',
                        'App\Models\TowerCase' => 'Gabinete',
                        'App\Models\Splitter' => 'Splitter',
                        'App\Models\AudioDevice' => 'Audio',
                        'App\Models\SparePart' => 'Repuesto',
                    ])
                    ->multiple()
                    ->label('Tipo'),
                
                SelectFilter::make('status')
                    ->options([
                        'Operativo' => 'Operativo',
                        'Deficiente' => 'Deficiente',
                        'Retirado' => 'Retirado',
                    ])
                    ->label('Estado'),
                
                \Filament\Tables\Filters\Filter::make('input_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('input_from')
                            ->label('Entrada desde'),
                        \Filament\Forms\Components\DatePicker::make('input_until')
                            ->label('Entrada hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['input_from'], fn ($query, $date) => $query->whereDate('input_date', '>=', $date))
                            ->when($data['input_until'], fn ($query, $date) => $query->whereDate('input_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['input_from'] ?? null) {
                            $indicators[] = 'Entrada desde ' . \Carbon\Carbon::parse($data['input_from'])->format('d/m/Y');
                        }
                        if ($data['input_until'] ?? null) {
                            $indicators[] = 'Entrada hasta ' . \Carbon\Carbon::parse($data['input_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
                    
                \Filament\Tables\Filters\Filter::make('output_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('output_from')
                            ->label('Salida desde'),
                        \Filament\Forms\Components\DatePicker::make('output_until')
                            ->label('Salida hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['output_from'], fn ($query, $date) => $query->whereDate('output_date', '>=', $date))
                            ->when($data['output_until'], fn ($query, $date) => $query->whereDate('output_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['output_from'] ?? null) {
                            $indicators[] = 'Salida desde ' . \Carbon\Carbon::parse($data['output_from'])->format('d/m/Y');
                        }
                        if ($data['output_until'] ?? null) {
                            $indicators[] = 'Salida hasta ' . \Carbon\Carbon::parse($data['output_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver')
                        ->authorize(false),
                    EditAction::make()
                        ->label('Editar')
                        ->authorize(false),
                    DeleteAction::make()
                        ->label('Eliminar')
                        ->authorize(false),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('primary')
                    ->button(),
            ])
            ->toolbarActions([
                \Filament\Actions\Action::make('exportExcel')
                    ->visible(fn () => auth()->user()?->can('ComponentExport'))
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        // Obtener registros filtrados
                        $records = $livewire->getFilteredTableQuery()->get();
                        
                        // Generar nombre de archivo
                        $filename = 'componentes_' . now()->format('Y-m-d_His') . '.xlsx';
                        
                        // Exportar
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ComponentsExport($records),
                            $filename,
                            \Maatwebsite\Excel\Excel::XLSX
                        );
                    }),
                
                \Filament\Actions\Action::make('exportCsv')
                    ->visible(fn () => auth()->user()?->can('ComponentExport'))
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->action(function ($livewire) {
                        // Obtener registros filtrados
                        $records = $livewire->getFilteredTableQuery()->get();
                        
                        // Generar nombre de archivo
                        $filename = 'componentes_' . now()->format('Y-m-d_His') . '.csv';
                        
                        // Exportar
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ComponentsExport($records),
                            $filename,
                            \Maatwebsite\Excel\Excel::CSV
                        );
                    }),
                
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('ComponentBulkDelete'))
                        ->label('Eliminar'),
                ])->label('Acciones en Lote'),
            ])
            ->emptyStateHeading('No hay ningún registro de componentes');
    }
}
