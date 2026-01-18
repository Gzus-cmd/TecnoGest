<?php

namespace App\Exports;

use App\Models\Transfer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeviceTransfersHistorySheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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

        return Transfer::where('deviceable_type', $deviceClass)
            ->where('deviceable_id', $this->device->id)
            ->with(['origin', 'destiny', 'registeredBy', 'updatedBy'])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function map($transfer): array
    {
        $origin = $transfer->origin ?
            ($transfer->origin->pavilion ? "{$transfer->origin->pavilion} - " : '') . $transfer->origin->name
            : '—';

        $destiny = $transfer->destiny ?
            ($transfer->destiny->pavilion ? "{$transfer->destiny->pavilion} - " : '') . $transfer->destiny->name
            : '—';

        $status = $this->device->location_id === $transfer->destiny_id ? 'Completado' : 'Pendiente';

        return [
            $transfer->user->name ?? '—',
            $origin,
            $destiny,
            $status,
            $transfer->date ? \Carbon\Carbon::parse($transfer->date)->format('d/m/Y') : '—',
            $transfer->reason ?? '—',
            $transfer->registeredBy->name ?? '—',
            $transfer->updatedBy->name ?? '—',
        ];
    }

    public function headings(): array
    {
        return [
            'Responsable',
            'Origen',
            'Destino',
            'Estado',
            'Fecha',
            'Motivo',
            'Registrado Por',
            'Actualizado Por',
        ];
    }

    public function title(): string
    {
        return 'Historial de Traslados';
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
            'B' => 30,
            'C' => 30,
            'D' => 15,
            'E' => 15,
            'F' => 35,
            'G' => 25,
            'H' => 25,
        ];
    }
}
