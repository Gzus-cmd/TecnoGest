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

class ComponentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $records;

    public function __construct(Collection $records)
    {
        // Aplicar eager loading para evitar N+1 queries
        $this->records = $records->load([
            'componentable',
            'provider',
            'computers.location',
            'printers.location',
            'projectors.location'
        ]);
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'N° de Serie',
            'Tipo de Componente',
            'Marca',
            'Modelo',
            'Estado',
            'Asignado a',
            'Proveedor',
            'Garantía (meses)',
            'Fecha de Entrada',
            'Fecha de Salida',
        ];
    }

    public function map($record): array
    {
        // Obtener tipo de componente
        $componentType = match (true) {
            str_contains($record->componentable_type, 'CPU') => 'Procesador',
            str_contains($record->componentable_type, 'GPU') => 'Tarjeta Gráfica',
            str_contains($record->componentable_type, 'RAM') => 'Memoria RAM',
            str_contains($record->componentable_type, 'ROM') => 'Almacenamiento',
            str_contains($record->componentable_type, 'PowerSupply') => 'Fuente de Poder',
            str_contains($record->componentable_type, 'NetworkAdapter') => 'Adaptador de Red',
            str_contains($record->componentable_type, 'Motherboard') => 'Placa Base',
            str_contains($record->componentable_type, 'Monitor') => 'Monitor',
            str_contains($record->componentable_type, 'Keyboard') => 'Teclado',
            str_contains($record->componentable_type, 'Mouse') => 'Ratón',
            str_contains($record->componentable_type, 'Stabilizer') => 'Estabilizador',
            str_contains($record->componentable_type, 'TowerCase') => 'Gabinete',
            str_contains($record->componentable_type, 'Splitter') => 'Splitter',
            str_contains($record->componentable_type, 'AudioDevice') => 'Dispositivo de Audio',
            str_contains($record->componentable_type, 'SparePart') => 'Repuesto',
            default => 'Otro',
        };

        // Obtener marca y modelo
        $brand = 'N/A';
        $model = 'N/A';
        try {
            $componentable = $record->componentable;
            if ($componentable) {
                $brand = $componentable->brand ?? 'N/A';
                $model = $componentable->model ?? 'N/A';
            }
        } catch (\Exception $e) {
            Log::warning("Error al exportar componente {$record->id}: " . $e->getMessage());
        }

        // Obtener asignación
        $assignment = 'Disponible';
        if ($record->status !== 'Retirado') {
            $computer = $record->computers->first();
            if ($computer) {
                $assignment = "PC: {$computer->serial} ({$computer->location->name})";
            } else {
                $printer = $record->printers->first();
                if ($printer) {
                    $assignment = "Impresora: {$printer->serial} ({$printer->location->name})";
                } else {
                    $projector = $record->projectors->first();
                    if ($projector) {
                        $assignment = "Proyector: {$projector->serial} ({$projector->location->name})";
                    }
                }
            }
        } else {
            $assignment = '—';
        }

        return [
            $record->serial,
            $componentType,
            $brand,
            $model,
            $record->status,
            $assignment,
            $record->provider->name ?? 'N/A',
            $record->warranty_months ?? 'N/A',
            $record->input_date ? \Carbon\Carbon::parse($record->input_date)->format('d/m/Y') : 'N/A',
            $record->output_date ? \Carbon\Carbon::parse($record->output_date)->format('d/m/Y') : 'N/A',
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
            'A' => 20,  // Serial
            'B' => 20,  // Tipo
            'C' => 15,  // Marca
            'D' => 25,  // Modelo
            'E' => 15,  // Estado
            'F' => 35,  // Asignado a
            'G' => 20,  // Proveedor
            'H' => 18,  // Garantía
            'I' => 15,  // Entrada
            'J' => 15,  // Salida
        ];
    }
}
