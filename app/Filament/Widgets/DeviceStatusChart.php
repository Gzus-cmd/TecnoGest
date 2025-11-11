<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use Filament\Widgets\ChartWidget;

class DeviceStatusChart extends ChartWidget
{
    protected ?string $heading = 'Estado de Dispositivos';
    
    protected static ?int $sort = 3;
    
   
    
    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        // Contar dispositivos por estado
        $active = Computer::where('status', 'Activo')->count() +
                  Printer::where('status', 'Activo')->count() +
                  Projector::where('status', 'Activo')->count();

        $inactive = Computer::where('status', 'Inactivo')->count() +
                    Printer::where('status', 'Inactivo')->count() +
                    Projector::where('status', 'Inactivo')->count();

        $maintenance = Computer::where('status', 'En Mantenimiento')->count() +
                       Printer::where('status', 'En Mantenimiento')->count() +
                       Projector::where('status', 'En Mantenimiento')->count();

        $decommissioned = Computer::where('status', 'Desmantelado')->count() +
                          Printer::where('status', 'Desmantelado')->count() +
                          Projector::where('status', 'Desmantelado')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Dispositivos',
                    'data' => [$active, $inactive, $maintenance, $decommissioned],
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
