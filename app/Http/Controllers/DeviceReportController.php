<?php

namespace App\Http\Controllers;

use App\Exports\DeviceFullReportExport;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DeviceReportController extends Controller
{
    public function fullReport(Request $request, string $type, int $id)
    {
        // Obtener el dispositivo según el tipo
        $device = match($type) {
            'computer' => Computer::findOrFail($id),
            'printer' => Printer::findOrFail($id),
            'projector' => Projector::findOrFail($id),
            default => abort(404, 'Tipo de dispositivo no válido'),
        };

        // Nombre descriptivo según el tipo
        $typeLabel = match($type) {
            'computer' => 'computadora',
            'printer' => 'impresora',
            'projector' => 'proyector',
        };

        $filename = "reporte_completo_{$typeLabel}_{$device->serial}_" . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new DeviceFullReportExport($device, $type), $filename);
    }
}
