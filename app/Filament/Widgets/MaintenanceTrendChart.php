<?php

namespace App\Filament\Widgets;

use App\Models\Maintenance;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class MaintenanceTrendChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected ?string $heading = 'Tendencia de Mantenimientos';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = '6m';
    
    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        // Si es 1 mes o 3 meses, mostrar por días
        if (in_array($this->filter, ['1m', '3m'])) {
            $days = match($this->filter) {
                '1m' => 30,
                '3m' => 90,
                default => 30,
            };
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayLabel = $date->locale('es')->format('d M');
                
                $preventive = Maintenance::where('type', 'Preventivo')
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
                    
                $corrective = Maintenance::where('type', 'Correctivo')
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
                
                $labels[] = $dayLabel;
                $data['preventive'][] = $preventive;
                $data['corrective'][] = $corrective;
            }
        } else {
            // Para 6 meses, 1 año y 2 años, mostrar por meses
            $months = match($this->filter) {
                '6m' => 6,
                '1y' => 12,
                'all' => 24,
                default => 6,
            };
            
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->locale('es')->format('M Y');
                
                $preventive = Maintenance::where('type', 'Preventivo')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                    
                $corrective = Maintenance::where('type', 'Correctivo')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                
                $labels[] = $monthName;
                $data['preventive'][] = $preventive;
                $data['corrective'][] = $corrective;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Preventivo',
                    'data' => $data['preventive'],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Correctivo',
                    'data' => $data['corrective'],
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '1m' => 'Último mes',
            '3m' => 'Últimos 3 meses',
            '6m' => 'Últimos 6 meses',
            '1y' => 'Último año',
            'all' => 'Últimos 2 años',
        ];
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
