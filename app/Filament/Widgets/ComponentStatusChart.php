<?php

namespace App\Filament\Widgets;

use App\Models\Component;
use Filament\Widgets\ChartWidget;

class ComponentStatusChart extends ChartWidget
{
    protected ?string $heading = 'Estado de Componentes en Inventario';
    
    protected static ?int $sort = 4;
    
    
    
    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        $operative = Component::where('status', 'Operativo')->count();
        $deficient = Component::where('status', 'Deficiente')->count();
        $retired = Component::where('status', 'Retirado')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Componentes',
                    'data' => [$operative, $deficient, $retired],
                    'backgroundColor' => [
                        '#10b981', // Verde - Operativo
                        '#f59e0b', // Amarillo - Deficiente
                        '#ef4444', // Rojo - Retirado
                    ],
                    'borderColor' => [
                        '#059669',
                        '#d97706',
                        '#dc2626',
                    ],
                ],
            ],
            'labels' => ['Operativo', 'Deficiente', 'Retirado'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
