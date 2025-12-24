<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Component;
use App\Models\Maintenance;
use App\Models\Transfer;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class MonthlyActivity extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        // Optimización: Obtener conteos de dispositivos con una sola query usando UNION
        $deviceStats = DB::select("
            SELECT 
                CASE 
                    WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 'current'
                    WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 'last'
                END as period,
                COUNT(*) as count
            FROM (
                SELECT created_at FROM computers
                UNION ALL
                SELECT created_at FROM printers
                UNION ALL
                SELECT created_at FROM projectors
            ) as all_devices
            WHERE (MONTH(created_at) = ? AND YEAR(created_at) = ?)
               OR (MONTH(created_at) = ? AND YEAR(created_at) = ?)
            GROUP BY period
        ", [
            $currentMonth, $currentYear, $lastMonth, $lastYear,
            $currentMonth, $currentYear, $lastMonth, $lastYear
        ]);
        
        $newDevicesThisMonth = collect($deviceStats)->firstWhere('period', 'current')?->count ?? 0;
        $devicesLastMonth = collect($deviceStats)->firstWhere('period', 'last')?->count ?? 0;

        // Componentes agregados este mes
        $newComponentsThisMonth = Component::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Mantenimientos con una sola query
        $maintenanceStats = Maintenance::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Finalizado' AND MONTH(updated_at) = ? AND YEAR(updated_at) = ? THEN 1 ELSE 0 END) as completed
        ", [$currentMonth, $currentYear])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->first();
        
        $maintenancesThisMonth = $maintenanceStats->total ?? 0;
        $completedMaintenances = $maintenanceStats->completed ?? 0;

        // Traslados realizados este mes
        $transfersThisMonth = Transfer::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $deviceTrend = $devicesLastMonth > 0 
            ? round((($newDevicesThisMonth - $devicesLastMonth) / $devicesLastMonth) * 100, 1)
            : 0;

        return [
            Stat::make('Dispositivos Nuevos (Este Mes)', $newDevicesThisMonth)
                ->description($deviceTrend > 0 ? "+{$deviceTrend}% vs mes anterior" : "{$deviceTrend}% vs mes anterior")
                ->descriptionIcon($deviceTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($deviceTrend >= 0 ? 'success' : 'danger')
                ->chart([$devicesLastMonth, $newDevicesThisMonth]),
            
            Stat::make('Componentes Agregados (Este Mes)', $newComponentsThisMonth)
                ->description('Nuevos en inventario')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
            
            Stat::make('Mantenimientos (Este Mes)', $maintenancesThisMonth)
                ->description("{$completedMaintenances} completados")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($completedMaintenances === $maintenancesThisMonth ? 'success' : 'warning'),
            
            Stat::make('Traslados (Este Mes)', $transfersThisMonth)
                ->description('Movimientos de equipos')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary'),
        ];
    }
}
