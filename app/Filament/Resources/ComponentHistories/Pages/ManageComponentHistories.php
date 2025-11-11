<?php

namespace App\Filament\Resources\ComponentHistories\Pages;

use App\Filament\Resources\ComponentHistories\ComponentHistoryResource;
use App\Exports\ComponentHistoryExport;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ManageComponentHistories extends ManageRecords
{
    protected static string $resource = ComponentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->extraAttributes(['style' => 'color: white;'])
                ->action(function () {
                    return $this->exportToExcel();
                }),
            Action::make('exportCsv')
                ->label('Exportar CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->extraAttributes(['style' => 'color: white;'])
                ->action(function () {
                    return $this->exportToCsv();
                }),
        ];
    }
    
    protected function exportToExcel()
    {
        $query = $this->getFilteredTableQuery();
        $records = $query->get();
        
        $filename = 'historial_componentes_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new ComponentHistoryExport($records), $filename);
    }
    
    protected function exportToCsv()
    {
        // Obtener los datos filtrados de la tabla
        $query = $this->getFilteredTableQuery();
        $records = $query->get();
        
        // Crear el archivo CSV
        $filename = 'historial_componentes_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'Tipo de Componente',
                'Marca',
                'Modelo',
                'Serial Componente',
                'Tipo de Dispositivo',
                'Serial Dispositivo',
                'Ubicación',
                'Fecha de Asignación',
                'Estado'
            ]);
            
            // Datos
            foreach ($records as $record) {
                // Obtener detalles del componente
                $componentType = match ($record->componentable_type) {
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
                };
                
                $brand = 'N/A';
                $model = 'N/A';
                try {
                    $component = $record->componentable_type::find($record->componentable_id);
                    if ($component) {
                        $brand = $component->brand ?? 'N/A';
                        $model = $component->model ?? 'N/A';
                    }
                } catch (\Exception $e) {}
                
                // Obtener detalles del dispositivo
                $deviceType = 'N/A';
                $deviceSerial = 'N/A';
                $location = 'N/A';
                
                try {
                    if (str_contains($record->device_type, 'Computer')) {
                        $device = \App\Models\Computer::with('location')->find($record->device_id);
                        $deviceType = 'PC';
                    } elseif (str_contains($record->device_type, 'Printer')) {
                        $device = \App\Models\Printer::with('location')->find($record->device_id);
                        $deviceType = 'Impresora';
                    } elseif (str_contains($record->device_type, 'Projector')) {
                        $device = \App\Models\Projector::with('location')->find($record->device_id);
                        $deviceType = 'Proyector';
                    }
                    
                    if (isset($device) && $device) {
                        $deviceSerial = $device->serial;
                        $location = $device->location->name ?? 'Sin ubicación';
                    }
                } catch (\Exception $e) {}
                
                fputcsv($file, [
                    $componentType,
                    $brand,
                    $model,
                    $record->serial,
                    $deviceType,
                    $deviceSerial,
                    $location,
                    \Carbon\Carbon::parse($record->assigned_at)->format('d/m/Y H:i'),
                    $record->assignment_status
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
}
