<?php

namespace App\Filament\Widgets;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\Component;
use App\Models\Maintenance;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    // Actualizar cada 5 minutos
    protected ?string $pollingInterval = '5m';

    protected function getStats(): array
    {
        // Optimización: Una sola query por modelo con selectRaw para contar estados
        $computerStats = Computer::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Activo' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'Inactivo' THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN status = 'En Mantenimiento' THEN 1 ELSE 0 END) as maintenance
        ")->first();
        
        $printerStats = Printer::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Activo' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'Inactivo' THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN status = 'En Mantenimiento' THEN 1 ELSE 0 END) as maintenance
        ")->first();
        
        $projectorStats = Projector::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Activo' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'Inactivo' THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN status = 'En Mantenimiento' THEN 1 ELSE 0 END) as maintenance
        ")->first();
        
        $componentStats = Component::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Operativo' THEN 1 ELSE 0 END) as operative,
            SUM(CASE WHEN status = 'Deficiente' THEN 1 ELSE 0 END) as deficient
        ")->first();
        
        $maintenanceStats = Maintenance::selectRaw("
            SUM(CASE WHEN status = 'Pendiente' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'En Proceso' THEN 1 ELSE 0 END) as in_progress
        ")->first();

        // Extraer valores
        $activeComputers = $computerStats->active ?? 0;
        $inactiveComputers = $computerStats->inactive ?? 0;
        $maintenanceComputers = $computerStats->maintenance ?? 0;
        $totalComputers = $computerStats->total ?? 0;

        $activePrinters = $printerStats->active ?? 0;
        $inactivePrinters = $printerStats->inactive ?? 0;
        $maintenancePrinters = $printerStats->maintenance ?? 0;
        $totalPrinters = $printerStats->total ?? 0;

        $activeProjectors = $projectorStats->active ?? 0;
        $inactiveProjectors = $projectorStats->inactive ?? 0;
        $maintenanceProjectors = $projectorStats->maintenance ?? 0;
        $totalProjectors = $projectorStats->total ?? 0;

        $totalComponents = $componentStats->total ?? 0;
        $operativeComponents = $componentStats->operative ?? 0;
        $deficientComponents = $componentStats->deficient ?? 0;

        $pendingMaintenances = $maintenanceStats->pending ?? 0;
        $inProgressMaintenances = $maintenanceStats->in_progress ?? 0;

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