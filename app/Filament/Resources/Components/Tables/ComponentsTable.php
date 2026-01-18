<?php

namespace App\Filament\Resources\Components\Tables;

use Illuminate\Support\Facades\Auth;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use \Filament\Actions\Action;

class ComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('componentable_type')
                    ->label('Tipo / Serial')
                    ->formatStateUsing(fn($state) => match (true) {
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
                    ->description(fn($record) => "🔢 " . ($record->serial ?? 'N/A'))
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
                    ->color(fn(string $state): string => match ($state) {
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
                    ->color(fn(?string $state): string => $state === 'Disponible' ? 'success' : ($state === null ? 'gray' : 'info'))
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
                    ->description(fn($record) => $record->output_date ? "📤 " . $record->output_date->format('d/m/Y') : '📤 —')
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
                        'CPU' => 'Procesador',
                        'GPU' => 'Tarjeta Gráfica',
                        'RAM' => 'Memoria RAM',
                        'ROM' => 'Almacenamiento',
                        'PowerSupply' => 'Fuente de Poder',
                        'NetworkAdapter' => 'Adaptador de Red',
                        'Motherboard' => 'Placa Base',
                        'Monitor' => 'Monitor',
                        'Keyboard' => 'Teclado',
                        'Mouse' => 'Ratón',
                        'Stabilizer' => 'Estabilizador',
                        'TowerCase' => 'Gabinete',
                        'Splitter' => 'Splitter',
                        'AudioDevice' => 'Audio',
                        'SparePart' => 'Repuesto',
                    ])
                    ->multiple()
                    ->preload()
                    ->label('Tipo'),

                SelectFilter::make('status')
                    ->options([
                        'Operativo' => 'Operativo',
                        'Deficiente' => 'Deficiente',
                        'Retirado' => 'Retirado',
                    ])
                    ->label('Estado'),

                Filter::make('input_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('input_from')
                            ->label('Entrada desde'),
                        \Filament\Forms\Components\DatePicker::make('input_until')
                            ->label('Entrada hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['input_from'], fn($query, $date) => $query->whereDate('input_date', '>=', $date))
                            ->when($data['input_until'], fn($query, $date) => $query->whereDate('input_date', '<=', $date));
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

                Filter::make('output_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('output_from')
                            ->label('Salida desde'),
                        \Filament\Forms\Components\DatePicker::make('output_until')
                            ->label('Salida hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['output_from'], fn($query, $date) => $query->whereDate('output_date', '>=', $date))
                            ->when($data['output_until'], fn($query, $date) => $query->whereDate('output_date', '<=', $date));
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
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver'),
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
                Action::make('exportExcel')
                    ->visible(fn() => Auth::user()?->can('ComponentExport'))
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

                Action::make('exportCsv')
                    ->visible(fn() => Auth::user()?->can('ComponentExport'))
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
                        ->visible(fn() => Auth::user()?->can('ComponentBulkDelete'))
                        ->label('Eliminar'),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de componentes');
    }
}
