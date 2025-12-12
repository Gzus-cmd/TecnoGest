<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Component;
use App\Models\Maintenance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Computadoras
        $activeComputers = Computer::where('status', 'Activo')->count();
        $inactiveComputers = Computer::where('status', 'Inactivo')->count();
        $maintenanceComputers = Computer::where('status', 'En Mantenimiento')->count();
        $totalComputers = $activeComputers + $inactiveComputers + $maintenanceComputers;

        // Impresoras
        $activePrinters = Printer::where('status', 'Activo')->count();
        $inactivePrinters = Printer::where('status', 'Inactivo')->count();
        $maintenancePrinters = Printer::where('status', 'En Mantenimiento')->count();
        $totalPrinters = $activePrinters + $inactivePrinters + $maintenancePrinters;

        // Proyectores
        $activeProjectors = Projector::where('status', 'Activo')->count();
        $inactiveProjectors = Projector::where('status', 'Inactivo')->count();
        $maintenanceProjectors = Projector::where('status', 'En Mantenimiento')->count();
        $totalProjectors = $activeProjectors + $inactiveProjectors + $maintenanceProjectors;

        // Componentes
        $totalComponents = Component::count();
        $operativeComponents = Component::where('status', 'Operativo')->count();
        $deficientComponents = Component::where('status', 'Deficiente')->count();

        // Mantenimientos
        $pendingMaintenances = Maintenance::where('status', 'Pendiente')->count();
        $inProgressMaintenances = Maintenance::where('status', 'En Proceso')->count();

        // Total de dispositivos
        $totalDevices = $totalComputers + $totalPrinters + $totalProjectors;
        $activeDevices = $activeComputers + $activePrinters + $activeProjectors;

        // Porcentaje de dispositivos activos
        $activePercentage = $totalDevices > 0 ? round(($activeDevices / $totalDevices) * 100, 1) : 0;

        return [
            
            Stat::make('Computadoras', $totalComputers)
            ->description("{$activeComputers} activas | {$inactiveComputers} inactivas | {$maintenanceComputers} en mantenimiento")
            ->descriptionIcon('heroicon-m-cpu-chip')
            ->color($activeComputers > $inactiveComputers ? 'success' : 'warning'),
            
            Stat::make('Impresoras', $totalPrinters)
            ->description("{$activePrinters} activas | {$inactivePrinters} inactivas | {$maintenancePrinters} en mantenimiento")
            ->descriptionIcon('heroicon-m-printer')
            ->color($activePrinters > 0 ? 'success' : 'gray'),
            
            Stat::make('Proyectores', $totalProjectors)
            ->description("{$activeProjectors} activos | {$inactiveProjectors} inactivos | {$maintenanceProjectors} en mantenimiento")
            ->descriptionIcon('heroicon-m-play')
            ->color($activeProjectors > 0 ? 'success' : 'gray'),
            
            Stat::make('Total de Dispositivos', $totalDevices)
                ->description("{$activeDevices} activos ({$activePercentage}%)")
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('primary')
                ->chart([7, 12, 15, 18, 20, 22, $totalDevices]),
                
            Stat::make('Componentes', $totalComponents)
                ->description("{$operativeComponents} operativos | {$deficientComponents} deficientes")
                ->descriptionIcon('heroicon-m-puzzle-piece')
                ->color($deficientComponents > 5 ? 'warning' : 'success'),
            
            Stat::make('Mantenimientos', $pendingMaintenances + $inProgressMaintenances)
                ->description("{$pendingMaintenances} pendientes | {$inProgressMaintenances} en proceso")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($pendingMaintenances > 10 ? 'danger' : ($pendingMaintenances > 5 ? 'warning' : 'success')),
        ];
    }
}