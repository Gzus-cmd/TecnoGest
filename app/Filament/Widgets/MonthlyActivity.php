<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Component;
use App\Models\Maintenance;
use App\Models\Transfer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyActivity extends BaseWidget
{
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Dispositivos agregados este mes
        $newDevicesThisMonth = 
            Computer::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count() +
            Printer::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count() +
            Projector::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

        // Componentes agregados este mes
        $newComponentsThisMonth = Component::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Mantenimientos realizados este mes
        $maintenancesThisMonth = Maintenance::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $completedMaintenances = Maintenance::where('status', 'Finalizado')
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->count();

        // Traslados realizados este mes
        $transfersThisMonth = Transfer::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        // Mes anterior para comparación
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        $devicesLastMonth = 
            Computer::whereMonth('created_at', $lastMonth)
                ->whereYear('created_at', $lastYear)
                ->count() +
            Printer::whereMonth('created_at', $lastMonth)
                ->whereYear('created_at', $lastYear)
                ->count() +
            Projector::whereMonth('created_at', $lastMonth)
                ->whereYear('created_at', $lastYear)
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
