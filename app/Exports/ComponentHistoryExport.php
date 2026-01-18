<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComponentHistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $records;

    public function __construct(Collection $records)
    {
        // Aplicar eager loading para evitar N+1 queries
        $this->records = $records->load([
            'componentable'
        ]);
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Tipo de Componente',
            'Marca',
            'Modelo',
            'Serial Componente',
            'Tipo de Dispositivo',
            'Serial Dispositivo',
            'Ubicación',
            'Fecha de Asignación',
            'Estado'
        ];
    }

    public function map($record): array
    {
        // Obtener detalles del componente
        $componentType = match ($record->componentable_type) {
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
        };

        $brand = 'N/A';
        $model = 'N/A';
        try {
            $component = $record->componentable_type::find($record->componentable_id);
            if ($component) {
                $brand = $component->brand ?? 'N/A';
                $model = $component->model ?? 'N/A';
            }
        } catch (\Exception $e) {
            Log::warning("Error al exportar historial de componente {$record->id}: " . $e->getMessage());
        }

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
        } catch (\Exception $e) {
            Log::warning("Error al exportar dispositivo del historial: " . $e->getMessage());
        }

        return [
            $componentType,
            $brand,
            $model,
            $record->serial,
            $deviceType,
            $deviceSerial,
            $location,
            \Carbon\Carbon::parse($record->assigned_at)->format('d/m/Y H:i'),
            $record->assignment_status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 25,
            'H' => 20,
            'I' => 15,
        ];
    }
}
