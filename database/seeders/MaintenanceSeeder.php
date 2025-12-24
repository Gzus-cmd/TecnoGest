<?php

namespace Database\Seeders;

use App\Models\Maintenance;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $workshopLocation = Location::where('is_workshop', true)->first();
        
        if (!$user) {
            $this->command->warn('⚠️ No hay usuarios. Ejecuta DatabaseSeeder primero.');
            return;
        }

        // Obtener dispositivos activos
        $computers = Computer::where('status', 'Activo')->limit(3)->get();
        $printers = Printer::where('status', 'Activo')->limit(2)->get();
        $projectors = Projector::where('status', 'Activo')->limit(1)->get();

        $maintenanceTypes = ['Preventivo', 'Correctivo'];
        $statuses = ['Pendiente', 'En Proceso', 'Finalizado'];
        $maintenancesCreated = 0;

        // Crear mantenimientos para computadoras
        foreach ($computers as $computer) {
            $type = collect($maintenanceTypes)->random();
            $status = collect($statuses)->random();
            $requiresWorkshop = $type === 'Correctivo' ? true : false;
            
            // Guardar estado anterior del dispositivo
            $previousStatus = $computer->status;
            
            // Si requiere taller, cambiar el estado del dispositivo a "En Mantenimiento"
            if ($requiresWorkshop && in_array($status, ['Pendiente', 'En Proceso'])) {
                $computer->update(['status' => 'En Mantenimiento']);
            }

            Maintenance::create([
                'type' => $type,
                'deviceable_type' => Computer::class,
                'deviceable_id' => $computer->id,
                'registered_by' => $user->id,
                'status' => $status,
                'description' => $this->getMaintenanceDescription($type),
                'requires_workshop' => $requiresWorkshop,
                'workshop_location_id' => $requiresWorkshop ? $workshopLocation?->id : null,
                'device_previous_status' => $previousStatus,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            $maintenancesCreated++;
        }

        // Crear mantenimientos para impresoras
        foreach ($printers as $printer) {
            $type = collect($maintenanceTypes)->random();
            $status = collect($statuses)->random();
            $requiresWorkshop = $type === 'Correctivo' ? true : false;
            
            $previousStatus = $printer->status;
            
            if ($requiresWorkshop && in_array($status, ['Pendiente', 'En Proceso'])) {
                $printer->update(['status' => 'En Mantenimiento']);
            }

            Maintenance::create([
                'type' => $type,
                'deviceable_type' => Printer::class,
                'deviceable_id' => $printer->id,
                'registered_by' => $user->id,
                'status' => $status,
                'description' => $this->getMaintenanceDescription($type, 'impresora'),
                'requires_workshop' => $requiresWorkshop,
                'workshop_location_id' => $requiresWorkshop ? $workshopLocation?->id : null,
                'device_previous_status' => $previousStatus,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            $maintenancesCreated++;
        }

        // Crear mantenimientos para proyectores
        foreach ($projectors as $projector) {
            $type = collect($maintenanceTypes)->random();
            $status = collect($statuses)->random();
            $requiresWorkshop = false; // Proyectores generalmente se mantienen en sitio
            
            $previousStatus = $projector->status;

            Maintenance::create([
                'type' => $type,
                'deviceable_type' => Projector::class,
                'deviceable_id' => $projector->id,
                'registered_by' => $user->id,
                'status' => $status,
                'description' => $this->getMaintenanceDescription($type, 'proyector'),
                'requires_workshop' => $requiresWorkshop,
                'device_previous_status' => $previousStatus,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            $maintenancesCreated++;
        }

        $this->command->info("✅ Mantenimientos creados: {$maintenancesCreated}");
        $this->command->info("   📋 Tipos: Preventivo y Correctivo");
        $this->command->info("   🔧 Estados: Pendiente, En Proceso, Finalizado");
    }

    /**
     * Obtener descripción de mantenimiento según el tipo
     */
    private function getMaintenanceDescription(string $type, string $deviceType = 'computadora'): string
    {
        $descriptions = [
            'Preventivo' => [
                'computadora' => 'Limpieza de componentes internos, actualización de software y verificación de hardware',
                'impresora' => 'Limpieza de cabezales, calibración y cambio de tóner',
                'proyector' => 'Limpieza de filtros, calibración de imagen y verificación de lámpara',
            ],
            'Correctivo' => [
                'computadora' => 'Reparación de falla en el sistema, reemplazo de componente defectuoso',
                'impresora' => 'Reparación de atasco de papel, reemplazo de rodillos',
                'proyector' => 'Reparación de sistema de enfriamiento, ajuste de lente',
            ],
        ];

        return $descriptions[$type][$deviceType] ?? 'Mantenimiento general del equipo';
    }
}
