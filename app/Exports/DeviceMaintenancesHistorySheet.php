<?php

namespace App\Exports;

use App\Models\Maintenance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeviceMaintenancesHistorySheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
        $deviceClass = match ($this->type) {
            'computer' => 'Computer',
            'printer' => 'Printer',
            'projector' => 'Projector',
        };

        return Maintenance::where('deviceable_type', $deviceClass)
            ->where('deviceable_id', $this->device->id)
            ->with(['registeredBy', 'updatedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->type,
            $maintenance->status,
            $maintenance->requires_workshop ? 'Sí' : 'No',
            $maintenance->description ?? '—',
            $maintenance->registeredBy->name ?? '—',
            $maintenance->updatedBy->name ?? '—',
            $maintenance->created_at->format('d/m/Y H:i'),
            $maintenance->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'Tipo',
            'Estado',
            'Requiere Taller',
            'Descripción',
            'Registrado Por',
            'Actualizado Por',
            'Fecha de Registro',
            'Última Actualización',
        ];
    }

    public function title(): string
    {
        return 'Historial de Mantenimientos';
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
            'A' => 20,
            'B' => 15,
            'C' => 18,
            'D' => 40,
            'E' => 25,
            'F' => 25,
            'G' => 20,
            'H' => 20,
        ];
    }
}
