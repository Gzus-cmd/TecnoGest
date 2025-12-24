<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CustomPermissionsSeeder extends Seeder
{
    /**
     * Los permisos personalizados para acciones específicas de cada recurso.
     * Estas son acciones que van MÁS ALLÁ de las CRUD estándar de Filament Shield.
     * Usamos PascalCase para ser consistentes con Filament Shield.
     */
    protected array $permissions = [
        // ========================================
        // COMPUTADORAS (Computers)
        // ========================================
        'ComputerAssignPeripheral',      // Asignar periférico a computadora
        'ComputerDismantle',              // Desmantelar computadora
        'ComputerUpdateSystem',           // Actualizar componentes del sistema
        'ComputerViewComponents',         // Ver componentes de la computadora
        'ComputerViewHistory',            // Ver historial (componentes, mantenimientos, traslados)
        'ComputerGenerateReport',         // Generar reporte completo

        // ========================================
        // PERIFÉRICOS (Peripherals)
        // ========================================
        'PeripheralViewComponents',      // Ver componentes del periférico
        'PeripheralUpdateComponents',    // Actualizar componentes del periférico
        'PeripheralDismantle',            // Desmantelar periférico

        // ========================================
        // MANTENIMIENTOS (Maintenances)
        // ========================================
        'MaintenanceExecute',             // Ejecutar (cambiar a "En Proceso")
        'MaintenanceFinish',              // Finalizar mantenimiento

        // ========================================
        // TRASLADOS (Transfers)
        // ========================================
        'TransferExecute',                // Ejecutar traslado
        'TransferFinish',                 // Finalizar traslado

        // ========================================
        // IMPRESORAS (Printers)
        // ========================================
        'PrinterViewDetails',            // Ver detalles de la impresora
        'PrinterUpdateSpares',           // Actualizar repuestos
        'PrinterViewHistory',            // Ver historial
        'PrinterGenerateReport',         // Generar reporte completo
        'PrinterDismantle',               // Desmantelar impresora

        // ========================================
        // PROYECTORES (Projectors)
        // ========================================
        'ProjectorViewDetails',          // Ver detalles del proyector
        'ProjectorUpdateSpares',         // Actualizar repuestos
        'ProjectorViewHistory',          // Ver historial
        'ProjectorGenerateReport',       // Generar reporte completo
        'ProjectorDismantle',             // Desmantelar proyector

        // ========================================
        // HISTORIAL DE COMPONENTES (Component Histories)
        // ========================================
        'ComponentHistoryExport',        // Exportar a Excel/CSV

        // ========================================
        // REPORTES Y DATOS (Reports & Data)
        // ========================================
        'ViewAdvancedReports',           // Ver reportes avanzados
        'ExportData',                     // Exportar datos
        'ManageSystemSettings',          // Gestionar configuraciones del sistema
    ];

    public function run(): void
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Creando permisos personalizados...');

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('Se crearon ' . count($this->permissions) . ' permisos personalizados.');
    }
}
