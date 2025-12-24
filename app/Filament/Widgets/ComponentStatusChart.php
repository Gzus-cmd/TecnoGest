<?php

namespace App\Filament\Widgets;

use App\Models\Component;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class ComponentStatusChart extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Estado de Componentes en Inventario';
    
    protected static ?int $sort = 4;
    
    
    
    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        // Optimización: Una sola query con selectRaw
        $stats = Component::selectRaw("
            SUM(CASE WHEN status = 'Operativo' THEN 1 ELSE 0 END) as operative,
            SUM(CASE WHEN status = 'Deficiente' THEN 1 ELSE 0 END) as deficient,
            SUM(CASE WHEN status = 'Retirado' THEN 1 ELSE 0 END) as retired
        ")->first();
        
        $operative = $stats->operative ?? 0;
        $deficient = $stats->deficient ?? 0;
        $retired = $stats->retired ?? 0;

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
