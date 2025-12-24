<?php

namespace App\Filament\Resources\Printers\Tables;

use App\Models\Component;
use App\Models\PrinterModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrintersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('modelo.brand')
                    ->label('Marca / Modelo')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => "🏷️ " . ($record->modelo?->model ?? 'N/A')),
                TextColumn::make('serial')
                    ->searchable()
                    ->label('Número de Serie'),
                TextColumn::make('location.name')
                    ->searchable()
                    ->label('Departamento'),
                TextColumn::make('ip_address')
                    ->searchable()
                    ->label('Dirección IP'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Inactivo' => 'gray',
                        'En Mantenimiento' => 'warning',
                        'Activo' => 'success',
                        'Desmantelado' => 'danger',
                    })
                    ->label('Estado'),
                TextColumn::make('input_date')
                    ->label('Entrada / Salida')
                    ->date('d/m/Y')
                    ->description(fn ($record) => $record->output_date ? "📤 " . $record->output_date->format('d/m/Y') : '📤 —')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Registro')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualización')
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

                Action::make('verDetalles')
                        ->label('Ver')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->visible(fn () => auth()->user()?->can('PrinterViewDetails'))
                        ->modalHeading('Detalles de la Impresora')
                        ->modalWidth('6xl')
                        ->modalSubmitAction(false)
                        ->infolist(function ($record) {
                        // Cargar componentes con sus relaciones
                        $record->load(['components.componentable', 'modelo']);
                        
                        $spareParts = $record->components->where('componentable_type', 'App\Models\SparePart');
                        
                        // Verificar si algún repuesto es aftermarket (no original de fábrica)
                        $hasAftermarketParts = $spareParts->filter(function ($component) use ($record) {
                            $sparePart = $component->componentable;
                            // Consideramos aftermarket si la marca del repuesto NO coincide con la marca del modelo de impresora
                            return $sparePart && $sparePart->brand !== $record->modelo->brand;
                        })->isNotEmpty();

                        return [
                            // SECCIÓN: INFORMACIÓN GENERAL
                            ViewEntry::make('general_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '🖨️ Información General',
                                    'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),
                            
                            Section::make()
                                ->schema([
                                    TextEntry::make('modelo_info')
                                        ->label('Modelo de Impresora')
                                        ->state(function () use ($record) {
                                            $modelo = $record->modelo;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$modelo->brand} {$modelo->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$record->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Departamento:</span> <span style='color: #9ca3af;'>{$record->location->name}</span></div>" .
                                                   ($record->ip_address ? "<div><span style='font-weight: 400; color: #6b7280;'>IP:</span> <span style='color: #9ca3af;'>{$record->ip_address}</span></div>" : "") .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$record->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(1),

                            // SECCIÓN: REPUESTOS
                            ViewEntry::make('repuestos_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '🔧 Repuestos Instalados',
                                    'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),
                            
                            Section::make()
                                ->schema([
                                    TextEntry::make('spare_parts_info')
                                        ->label('Repuestos')
                                        ->state(function () use ($spareParts, $record, $hasAftermarketParts) {
                                            if ($spareParts->isEmpty()) {
                                                return '<span style="color: #9ca3af;">No hay repuestos instalados</span>';
                                            }
                                            
                                            $html = "";
                                            
                                            // Mensaje de alerta si hay piezas aftermarket
                                            if ($hasAftermarketParts) {
                                                $html .= "<div style='background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin-bottom: 16px; border-radius: 4px;'>" .
                                                        "<span style='color: #92400e; font-weight: 600;'>⚠️ Esta impresora tiene repuestos que no son de fábrica (aftermarket)</span>" .
                                                        "</div>";
                                            }
                                            
                                            $html .= $spareParts->map(function ($component, $index) use ($record) {
                                                $sp = $component->componentable;
                                                $num = $index + 1;
                                                
                                                // Verificar si es aftermarket
                                                $isAftermarket = $sp->brand !== $record->modelo->brand;
                                                $borderColor = $isAftermarket ? '#f59e0b' : '#10b981'; // Naranja si es aftermarket, verde si es original
                                                $badge = $isAftermarket ? '<span style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px;">Aftermarket</span>' : '<span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px;">Original</span>';
                                                
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid {$borderColor};'>" .
                                                       "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>Repuesto #{$num}: {$sp->brand} {$sp->model} {$badge}</div>" .
                                                       "<div style='line-height: 1.6;'>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Tipo:</span> <span style='color: #9ca3af;'>{$sp->type}</span><br>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Número de Parte:</span> <span style='color: #9ca3af;'>" . ($sp->part_number ?? 'N/A') . "</span><br>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Serial Componente:</span> <span style='color: #9ca3af;'>{$component->serial}</span> | " .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$component->status}</span>" .
                                                       "</div></div>";
                                            })->join('');
                                            
                                            return $html;
                                        })
                                        ->html()
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ];
                    }),

                    Action::make('actualizarRepuestos')
                        ->label('Actualizar')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('info')
                        ->visible(fn () => auth()->user()?->can('PrinterUpdateSpares'))
                        ->modalHeading('Actualizar Repuestos de la Impresora')
                        ->modalDescription('Administre los repuestos instalados en esta impresora')
                        ->modalWidth('6xl')
                        ->modalSubmitActionLabel('Guardar Cambios')
                        ->modalCancelActionLabel('Cancelar')
                        ->form([
                        Grid::make(1)->schema([
                            Repeater::make('spare_parts')
                                ->label('Repuestos Instalados')
                                ->schema([
                                    Select::make('component_id')
                                        ->label('Repuesto')
                                        ->options(function ($record) {
                                            // Obtener IDs de repuestos actualmente asignados a esta impresora
                                            $currentSparePartIds = $record->components()
                                                ->where('components.componentable_type', 'App\Models\SparePart')
                                                ->pluck('components.id')
                                                ->toArray();
                                            
                                            // Obtener todos los componentes SparePart operativos
                                            $availableSpareParts = Component::where('componentable_type', 'App\Models\SparePart')
                                                ->where('status', 'Operativo')
                                                ->where(function ($query) use ($currentSparePartIds) {
                                                    $query->whereDoesntHave('printers')
                                                          ->orWhereHas('printers', function ($q) use ($currentSparePartIds) {
                                                              $q->whereIn('components.id', $currentSparePartIds);
                                                          });
                                                })
                                                ->get();
                                            
                                            return $availableSpareParts->mapWithKeys(function ($component) use ($currentSparePartIds, $record) {
                                                $sp = $component->componentable;
                                                $isAftermarket = $sp->brand !== $record->modelo->brand;
                                                $badge = $isAftermarket ? ' [Aftermarket]' : ' [Original]';
                                                $label = "{$sp->brand} {$sp->model} ({$sp->type}) - Serial: {$component->serial}{$badge}";
                                                
                                                if (in_array($component->id, $currentSparePartIds)) {
                                                    $label .= ' (ACTUAL)';
                                                }
                                                
                                                return [$component->id => $label];
                                            });
                                        })
                                        ->searchable()
                                        ->required()
                                        ->distinct(),
                                ])
                                ->addActionLabel('Agregar Repuesto')
                                ->collapsible()
                                ->defaultItems(0),
                        ]),
                    ])
                    ->fillForm(function ($record): array {
                        return [
                            'spare_parts' => $record->components
                                ->where('componentable_type', 'App\Models\SparePart')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray(),
                        ];
                    })
                    ->action(function ($record, array $data): void {
                        // Obtener repuestos actuales
                        $currentSpareParts = $record->components()
                            ->where('componentable_type', 'App\Models\SparePart')
                            ->pluck('components.id')
                            ->toArray();

                        // Obtener nuevos repuestos del formulario
                        $newSparePartIds = array_column($data['spare_parts'] ?? [], 'component_id');

                        // Identificar repuestos que fueron removidos
                        $componentsToRemove = array_diff($currentSpareParts, $newSparePartIds);

                        // Marcar como removidos
                        if (!empty($componentsToRemove)) {
                            $record->components()->updateExistingPivot($componentsToRemove, [
                                'status' => 'Removido',
                                'removed_by' => Auth::id(),
                            ]);
                        }

                        // Asignar o actualizar repuestos
                        $pivotData = [
                            'assigned_at' => now(),
                            'status' => 'Vigente',
                            'assigned_by' => Auth::id(),
                        ];

                        foreach ($newSparePartIds as $componentId) {
                            if ($componentId) {
                                $exists = $record->components()->wherePivot('component_id', $componentId)->exists();
                                if ($exists) {
                                    $record->components()->updateExistingPivot($componentId, $pivotData);
                                } else {
                                    $record->components()->attach($componentId, $pivotData);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Repuestos actualizados')
                            ->success()
                            ->body('Los repuestos de la impresora han sido actualizados exitosamente.')
                            ->send();
                    }),

                ActionGroup::make([
                    

                    Action::make('verHistorial')
                        ->label('Historial')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->visible(fn () => auth()->user()?->can('PrinterViewHistory'))
                        ->modalHeading('Historial de la Impresora')
                        ->modalDescription(fn ($record) => "Seleccione el tipo de historial que desea consultar para {$record->serial}")
                        ->modalIcon('heroicon-o-clock')
                        ->modalWidth('md')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar')
                        ->extraModalFooterActions([
                        Action::make('historialComponentes')
                            ->label('Historial de Componentes')
                            ->icon('heroicon-o-cpu-chip')
                            ->color('info')
                            ->url(fn ($record): string => route('filament.admin.resources.component-histories.index', [
                                'filters' => [
                                    'device_id' => ['value' => 'Printer-' . $record->id],
                                ],
                            ]))
                            ->openUrlInNewTab(),
                        
                        Action::make('historialMantenimientos')
                            ->label('Historial de Mantenimientos')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->color('warning')
                            ->url(fn ($record): string => route('filament.admin.resources.maintenances.index', [
                                'filters' => [
                                    'deviceable_type' => ['value' => 'App\Models\Printer'],
                                    'deviceable_id' => ['value' => $record->id],
                                ],
                            ]))
                            ->openUrlInNewTab(),
                        
                        Action::make('historialTraslados')
                            ->label('Historial de Traslados')
                            ->icon('heroicon-o-arrow-path')
                            ->color('success')
                            ->url(fn ($record): string => route('filament.admin.resources.transfers.index', [
                                'filters' => [
                                    'deviceable_type' => ['value' => 'App\Models\Printer'],
                                    'deviceable_id' => ['value' => $record->id],
                                ]
                            ]))
                            ->openUrlInNewTab(),
                        
                        Action::make('generarReporte')
                            ->label('Generar Reporte Completo')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('danger')
                            ->visible(fn () => auth()->user()?->can('PrinterGenerateReport'))
                            ->url(fn ($record): string => route('devices.full-report', [
                                'type' => 'printer',
                                'id' => $record->id,
                            ])),
                    ]),

                    Action::make('desmantelar')
                        ->label('Desmantelar')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Desmantelar Impresora')
                        ->modalDescription(fn ($record) => "¿Está seguro de desmantelar la impresora {$record->serial}? Todos los repuestos vigentes serán removidos y la impresora pasará al estado 'Desmantelado'.")
                        ->modalSubmitActionLabel('Sí, desmantelar')
                        ->modalCancelActionLabel('Cancelar')
                        ->visible(fn ($record) => 
                            auth()->user()?->can('printer_dismantle') && 
                            $record->status === 'Inactivo'
                        )
                        ->action(function ($record) {
                            DB::transaction(function () use ($record) {
                                // Actualizar todos los componentes vigentes a "Removido"
                                DB::table('componentables')
                                    ->where('componentable_type', 'App\\Models\\Printer')
                                    ->where('componentable_id', $record->id)
                                    ->where('status', 'Vigente')
                                    ->update([
                                        'status' => 'Removido',
                                        'updated_at' => now()
                                    ]);
                                
                                // Cambiar el estado de la impresora a Desmantelado
                                $record->update(['status' => 'Desmantelado']);
                            });
                            
                            Notification::make()
                                ->title('Impresora desmantelada')
                                ->success()
                                ->body("La impresora {$record->serial} ha sido desmantelada exitosamente.")
                                ->send();
                        }),
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
            ->emptyStateHeading('No hay ningún registro de impresoras');
    }
}
