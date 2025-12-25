<?php

namespace App\Filament\Widgets;

use App\Models\Maintenance;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class MaintenanceTrendChart extends ChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;
    
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
        $data = ['preventive' => [], 'corrective' => []];
        $labels = [];
        
        // Si es 1 mes o 3 meses, mostrar por días
        if (in_array($this->filter, ['1m', '3m'])) {
            $days = match($this->filter) {
                '1m' => 30,
                '3m' => 90,
                default => 30,
            };
            
            $startDate = now()->subDays($days - 1)->startOfDay();
            
            // Una sola query para obtener todos los datos
            $results = Maintenance::selectRaw(
                'DATE(created_at) as date, type, COUNT(*) as count'
            )
                ->where('created_at', '>=', $startDate)
                ->groupBy('date', 'type')
                ->get()
                ->groupBy('date');
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateString = $date->toDateString();
                $dayLabel = $date->locale('es')->format('d M');
                
                $dayData = $results->get($dateString, collect());
                $preventive = $dayData->firstWhere('type', 'Preventivo')?->count ?? 0;
                $corrective = $dayData->firstWhere('type', 'Correctivo')?->count ?? 0;
                
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
            
            $startDate = now()->subMonths($months - 1)->startOfMonth();
            
            // Una sola query para obtener todos los datos
            $results = Maintenance::selectRaw(
                'EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, type, COUNT(*) as count'
            )
                ->where('created_at', '>=', $startDate)
                ->groupBy('year', 'month', 'type')
                ->get()
                ->groupBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));
            
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthKey = $date->year . '-' . str_pad($date->month, 2, '0', STR_PAD_LEFT);
                $monthName = $date->locale('es')->format('M Y');
                
                $monthData = $results->get($monthKey, collect());
                $preventive = $monthData->firstWhere('type', 'Preventivo')?->count ?? 0;
                $corrective = $monthData->firstWhere('type', 'Correctivo')?->count ?? 0;
                
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
