<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Location;
use App\Models\Printer;
use App\Models\Projector;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class DevicesByLocationChart extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Dispositivos por Ubicación';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';
    
    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        // Optimización: Usar withCount para cargar conteos en una sola query
        $locations = Location::withCount(['computers', 'printers', 'projectors'])
            ->having('computers_count', '>', 0)
            ->orHaving('printers_count', '>', 0)
            ->orHaving('projectors_count', '>', 0)
            ->get();
        
        $locationNames = [];
        $computerCounts = [];
        $printerCounts = [];
        $projectorCounts = [];

        foreach ($locations as $location) {
            $locationNames[] = $location->name;
            $computerCounts[] = $location->computers_count;
            $printerCounts[] = $location->printers_count;
            $projectorCounts[] = $location->projectors_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Computadoras',
                    'data' => $computerCounts,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ],
                [
                    'label' => 'Impresoras',
                    'data' => $printerCounts,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                ],
                [
                    'label' => 'Proyectores',
                    'data' => $projectorCounts,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                ],
            ],
            'labels' => $locationNames,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}
