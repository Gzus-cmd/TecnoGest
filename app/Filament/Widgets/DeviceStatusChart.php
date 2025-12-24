<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DeviceStatusChart extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Estado de Dispositivos';
    
    protected static ?int $sort = 3;
    
   
    
    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        // Optimización: Una sola query con UNION ALL para contar todos los estados
        $results = DB::select("
            SELECT status, COUNT(*) as count FROM (
                SELECT status FROM computers
                UNION ALL
                SELECT status FROM printers
                UNION ALL
                SELECT status FROM projectors
            ) AS all_devices
            GROUP BY status
        ");
        
        $statusCounts = [
            'Activo' => 0,
            'Inactivo' => 0,
            'En Mantenimiento' => 0,
            'Desmantelado' => 0,
        ];
        
        foreach ($results as $result) {
            if (isset($statusCounts[$result->status])) {
                $statusCounts[$result->status] = $result->count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Dispositivos',
                    'data' => [
                        $statusCounts['Activo'],
                        $statusCounts['Inactivo'],
                        $statusCounts['En Mantenimiento'],
                        $statusCounts['Desmantelado'],
                    ],
                    'backgroundColor' => [
                        '#10b981', // Verde - Activo
                        '#6b7280', // Gris - Inactivo
                        '#f59e0b', // Amarillo - Mantenimiento
                        '#ef4444', // Rojo - Desmantelado
                    ],
                    'borderColor' => [
                        '#059669',
                        '#4b5563',
                        '#d97706',
                        '#dc2626',
                    ],
                ],
            ],
            'labels' => ['Activo', 'Inactivo', 'En Mantenimiento', 'Desmantelado'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
