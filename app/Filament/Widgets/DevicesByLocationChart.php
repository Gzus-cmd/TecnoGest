<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Location;
use App\Models\Printer;
use App\Models\Projector;
use Filament\Widgets\ChartWidget;

class DevicesByLocationChart extends ChartWidget
{
    protected ?string $heading = 'Dispositivos por Ubicación';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';
    
    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        // Obtener todas las ubicaciones con sus dispositivos
        $locations = Location::all();
        
        $locationNames = [];
        $computerCounts = [];
        $printerCounts = [];
        $projectorCounts = [];

        foreach ($locations as $location) {
            $computersCount = Computer::where('location_id', $location->id)->count();
            $printersCount = Printer::where('location_id', $location->id)->count();
            $projectorsCount = Projector::where('location_id', $location->id)->count();
            
            // Solo agregar ubicaciones que tengan al menos un dispositivo
            if ($computersCount > 0 || $printersCount > 0 || $projectorsCount > 0) {
                $locationNames[] = $location->name;
                $computerCounts[] = $computersCount;
                $printerCounts[] = $printersCount;
                $projectorCounts[] = $projectorsCount;
            }
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
