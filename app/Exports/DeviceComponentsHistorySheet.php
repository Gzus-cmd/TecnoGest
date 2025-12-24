<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeviceComponentsHistorySheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $device;
    protected $type;

    public function __construct($device, string $type)
    {
        $this->device = $device;
        $this->type = $type;
    }

    public function collection()
    {
        $deviceClass = match($this->type) {
            'computer' => 'App\Models\Computer',
            'printer' => 'App\Models\Printer',
            'projector' => 'App\Models\Projector',
        };

        return DB::table('components')
            ->join('componentables', 'components.id', '=', 'componentables.component_id')
            ->where('componentables.componentable_type', $deviceClass)
            ->where('componentables.componentable_id', $this->device->id)
            ->orderBy('componentables.assigned_at', 'desc')
            ->select(
                'components.componentable_type',
                'components.componentable_id',
                'components.serial',
                'componentables.assigned_at',
                'componentables.status'
            )
            ->get();
    }

    public function map($row): array
    {
        // Mapear el tipo de componente a nombre legible
        $typeMap = [
            'App\Models\Motherboard' => 'Placa Base',
            'App\Models\CPU' => 'Procesador',
            'App\Models\GPU' => 'Tarjeta Gráfica',
            'App\Models\RAM' => 'Memoria RAM',
            'App\Models\ROM' => 'Almacenamiento',
            'App\Models\PowerSupply' => 'Fuente de Poder',
            'App\Models\TowerCase' => 'Gabinete',
            'App\Models\Monitor' => 'Monitor',
            'App\Models\Keyboard' => 'Teclado',
            'App\Models\Mouse' => 'Mouse',
            'App\Models\AudioDevice' => 'Dispositivo de Audio',
            'App\Models\NetworkAdapter' => 'Adaptador de Red',
            'App\Models\Stabilizer' => 'Estabilizador',
            'App\Models\Splitter' => 'Multicontacto',
        ];

        $componentType = $typeMap[$row->componentable_type] ?? 'Desconocido';

        // Intentar obtener la marca y modelo del componente
        $brand = '—';
        $model = '—';
        
        try {
            $componentClass = $row->componentable_type;
            if (class_exists($componentClass)) {
                $component = $componentClass::find($row->componentable_id);
                if ($component) {
                    $brand = $component->brand ?? '—';
                    $model = $component->model ?? '—';
                }
            }
        } catch (\Exception $e) {
            // Si hay error, dejamos los valores por defecto
        }

        return [
            $componentType,
            $brand,
            $model,
            $row->serial,
            \Carbon\Carbon::parse($row->assigned_at)->format('d/m/Y H:i'),
            $row->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Tipo de Componente',
            'Marca',
            'Modelo',
            'Serial',
            'Fecha de Asignación',
            'Estado',
        ];
    }

    public function title(): string
    {
        return 'Historial de Componentes';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 15,
        ];
    }
}
