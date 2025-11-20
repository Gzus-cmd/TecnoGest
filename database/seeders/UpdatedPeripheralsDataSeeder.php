<?php

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\Peripheral;
use App\Models\Component;
use App\Models\Monitor;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\AudioDevice;
use App\Models\Stabilizer;
use App\Models\Splitter;
use App\Models\Location;
use App\Models\Maintenance;
use Illuminate\Database\Seeder;

class UpdatedPeripheralsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔧 Creando periféricos completos con Audio, Stabilizer y Splitter...');
        
        $locations = Location::where('is_workshop', false)->get();
        
        if ($locations->isEmpty()) {
            $this->command->error('No hay ubicaciones disponibles');
            return;
        }
        
        // Obtener componentes operativos disponibles
        $availableAudio = Component::where('componentable_type', AudioDevice::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();
            
        $availableStabilizers = Component::where('componentable_type', Stabilizer::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();
            
        $availableSplitters = Component::where('componentable_type', Splitter::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();
            
        $availableMonitors = Component::where('componentable_type', Monitor::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();
            
        $availableKeyboards = Component::where('componentable_type', Keyboard::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();
            
        $availableMice = Component::where('componentable_type', Mouse::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();

        // Crear 8 periféricos completos
        $existingCount = Peripheral::count();
        
        for ($i = 1; $i <= 8; $i++) {
            $peripheral = Peripheral::create([
                'code' => 'PER-' . str_pad($existingCount + $i, 3, '0', STR_PAD_LEFT),
                'location_id' => $locations->random()->id,
                'status' => 'Activo',
            ]);

            $componentsToAttach = [];

            // Asignar 1-2 monitores
            $monitorCount = rand(1, 2);
            for ($j = 0; $j < $monitorCount && $availableMonitors->isNotEmpty(); $j++) {
                $monitor = $availableMonitors->shift();
                $componentsToAttach[$monitor->id] = [
                    'assigned_at' => now(),
                    'status' => 'Vigente',
                ];
            }

            // Asignar teclado
            if ($availableKeyboards->isNotEmpty()) {
                $keyboard = $availableKeyboards->shift();
                $componentsToAttach[$keyboard->id] = [
                    'assigned_at' => now(),
                    'status' => 'Vigente',
                ];
            }

            // Asignar mouse
            if ($availableMice->isNotEmpty()) {
                $mouse = $availableMice->shift();
                $componentsToAttach[$mouse->id] = [
                    'assigned_at' => now(),
                    'status' => 'Vigente',
                ];
            }

            // Asignar audio (80% de probabilidad)
            if ($availableAudio->isNotEmpty() && rand(1, 100) <= 80) {
                $audio = $availableAudio->shift();
                $componentsToAttach[$audio->id] = [
                    'assigned_at' => now(),
                    'status' => 'Vigente',
                ];
            }

            // Asignar stabilizer (70% de probabilidad)
            if ($availableStabilizers->isNotEmpty() && rand(1, 100) <= 70) {
                $stabilizer = $availableStabilizers->shift();
                $componentsToAttach[$stabilizer->id] = [
                    'assigned_at' => now(),
                    'status' => 'Vigente',
                ];
            }

            // Asignar splitter (50% de probabilidad)
            if ($availableSplitters->isNotEmpty() && rand(1, 100) <= 50) {
                $splitter = $availableSplitters->shift();
                $componentsToAttach[$splitter->id] = [
                    'assigned_at' => now(),
                    'status' => 'Vigente',
                ];
            }

            $peripheral->components()->attach($componentsToAttach);
            $this->command->info("   ✓ Peripheral {$peripheral->code} creado con " . count($componentsToAttach) . " componentes");
        }

        $this->command->info('');
        $this->command->info('🖥️ Creando computadoras en mantenimiento...');
        
        // Crear 3 computadoras en mantenimiento
        $this->createComputersInMaintenance(3);

        $this->command->info('');
        $this->command->info('🔨 Creando computadoras desmanteladas con historial...');
        
        // Crear 2 computadoras desmanteladas
        $this->createDismantledComputers(2);

        $this->command->info('');
        $this->command->info('✅ Datos actualizados correctamente!');
    }

    private function createComputersInMaintenance($count)
    {
        $workshopLocation = Location::where('is_workshop', true)->first();
        $normalLocations = Location::where('is_workshop', false)->get();
        $user = \App\Models\User::first();
        $os = \App\Models\OS::inRandomOrder()->first();
        
        if (!$workshopLocation) {
            // Crear ubicación de taller si no existe
            $workshopLocation = Location::create([
                'name' => 'Taller de Mantenimiento',
                'pavilion' => 'TALLER',
                'apartment' => 0,
                'is_workshop' => true,
            ]);
            $this->command->info('   ℹ️ Ubicación de taller creada');
        }

        if (!$user) {
            $this->command->warn('   ⚠️ No hay usuarios en el sistema');
            return;
        }

        // Obtener componentes disponibles para crear nuevas computadoras
        $availableCPUs = Component::where('componentable_type', \App\Models\CPU::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();
            
        $availableMBs = Component::where('componentable_type', \App\Models\Motherboard::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $existingCount = Computer::count();

        for ($i = 1; $i <= $count; $i++) {
            if ($availableCPUs->isEmpty() || $availableMBs->isEmpty()) {
                $this->command->warn('   ⚠️ No hay suficientes componentes disponibles');
                break;
            }

            $originalLocation = $normalLocations->random();

            // Crear nueva computadora como Activo primero
            $computer = Computer::create([
                'serial' => 'PC-' . str_pad($existingCount + $i, 5, '0', STR_PAD_LEFT),
                'ip_address' => '192.168.1.' . (100 + $existingCount + $i),
                'status' => 'Activo', // Primero Activo para pasar la validación
                'location_id' => $originalLocation->id,
                'os_id' => $os?->id,
            ]);

            // Asignar componentes básicos
            $cpu = $availableCPUs->shift();
            $mb = $availableMBs->shift();

            $computer->components()->attach([
                $cpu->id => ['assigned_at' => now()->subDays(30), 'status' => 'Vigente'],
                $mb->id => ['assigned_at' => now()->subDays(30), 'status' => 'Vigente'],
            ]);

            // Crear peripheral para la computadora EN LA UBICACIÓN ORIGINAL
            // (los periféricos NO van al taller, quedan disponibles)
            $peripheral = Peripheral::create([
                'code' => 'PER-' . str_pad(Peripheral::count() + 1, 3, '0', STR_PAD_LEFT),
                'location_id' => $originalLocation->id, // Queda en ubicación original
                'status' => 'Activo', // Disponible para reasignación
                'computer_id' => null, // Desvinculado (la PC está en taller)
            ]);

            // Crear registro de mantenimiento
            $maintenanceDate = now()->subDays(rand(5, 20));
            Maintenance::create([
                'type' => collect(['Preventivo', 'Correctivo'])->random(),
                'deviceable_type' => Computer::class,
                'deviceable_id' => $computer->id,
                'registered_by' => $user->id,
                'status' => 'En Proceso',
                'description' => collect([
                    'Actualización de sistema operativo y drivers',
                    'Limpieza interna y aplicación de pasta térmica',
                    'Reemplazo de componentes defectuosos',
                    'Optimización de rendimiento y verificación de temperatura',
                    'Diagnóstico general de hardware y pruebas de estrés',
                ])->random(),
                'requires_workshop' => true,
                'workshop_location_id' => $workshopLocation->id,
                'device_previous_status' => 'Activo',
                'created_at' => $maintenanceDate,
                'updated_at' => $maintenanceDate,
            ]);

            // Ahora sí cambiar a En Mantenimiento y mover SOLO LA PC al taller
            $computer->update([
                'status' => 'En Mantenimiento',
                'location_id' => $workshopLocation->id,
                'peripheral_id' => null, // Sin peripheral (está desvinculado)
            ]);

            // Crear transferencia de ida al taller
            \App\Models\Transfer::create([
                'deviceable_type' => Computer::class,
                'deviceable_id' => $computer->id,
                'origin_id' => $originalLocation->id,
                'destiny_id' => $workshopLocation->id,
                'date' => $maintenanceDate->copy()->subHours(2)->format('Y-m-d'),
                'reason' => 'Traslado para mantenimiento',
                'registered_by' => $user->id,
                'status' => 'Finalizado',
                'created_at' => $maintenanceDate->copy()->subHours(2),
                'updated_at' => $maintenanceDate->copy()->subHours(1),
            ]);

            $this->command->info("   ✓ {$computer->serial} creada en mantenimiento (taller)");
        }
    }

    private function createDismantledComputers($count)
    {
        $user = \App\Models\User::first();
        $locations = Location::where('is_workshop', false)->get();
        $workshopLocation = Location::where('is_workshop', true)->first();
        $os = \App\Models\OS::inRandomOrder()->first();
        
        // Obtener componentes disponibles
        $availableCPUs = Component::where('componentable_type', \App\Models\CPU::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();
            
        $availableMBs = Component::where('componentable_type', \App\Models\Motherboard::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();
            
        $availableRAMs = Component::where('componentable_type', \App\Models\RAM::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $existingCount = Computer::count();

        for ($i = 1; $i <= $count; $i++) {
            if ($availableCPUs->isEmpty() || $availableMBs->isEmpty()) {
                $this->command->warn('   ⚠️ No hay suficientes componentes disponibles');
                break;
            }

            $removedDate = now()->subMonths(rand(1, 6));
            $location = $locations->random();

            // Crear computadora como Inactiva primero (para pasar la validación)
            $computer = Computer::create([
                'serial' => 'PC-' . str_pad($existingCount + $i, 5, '0', STR_PAD_LEFT),
                'ip_address' => '192.168.1.' . (150 + $existingCount + $i),
                'status' => 'Inactivo', // Primero Inactivo para pasar validación
                'location_id' => $location->id,
                'os_id' => $os?->id,
            ]);

            // Asignar componentes que luego serán removidos
            $cpu = $availableCPUs->shift();
            $mb = $availableMBs->shift();
            $componentsToAttach = [
                $cpu->id => [
                    'assigned_at' => $removedDate->copy()->subMonths(12),
                    'status' => 'Removido'
                ],
                $mb->id => [
                    'assigned_at' => $removedDate->copy()->subMonths(12),
                    'status' => 'Removido'
                ],
            ];

            // Agregar RAMs si hay disponibles
            for ($j = 0; $j < 2 && $availableRAMs->isNotEmpty(); $j++) {
                $ram = $availableRAMs->shift();
                $componentsToAttach[$ram->id] = [
                    'assigned_at' => $removedDate->copy()->subMonths(12),
                    'status' => 'Removido'
                ];
            }

            $computer->components()->attach($componentsToAttach);

            // Crear peripheral desmantelado
            $peripheral = Peripheral::create([
                'code' => 'PER-' . str_pad(Peripheral::count() + 1, 3, '0', STR_PAD_LEFT),
                'location_id' => $location->id,
                'status' => 'Inactivo',
                'computer_id' => $computer->id,
            ]);

            $computer->update(['peripheral_id' => $peripheral->id]);

            // Crear registro de mantenimiento de desmontaje
            Maintenance::create([
                'type' => 'Correctivo',
                'deviceable_type' => Computer::class,
                'deviceable_id' => $computer->id,
                'registered_by' => $user->id,
                'status' => 'Finalizado',
                'description' => 'Desmontaje completo de equipo - Componentes retirados para reutilización debido a obsolescencia del equipo',
                'requires_workshop' => true,
                'workshop_location_id' => $workshopLocation?->id,
                'device_previous_status' => 'Inactivo',
                'created_at' => $removedDate->copy()->subDays(2),
                'updated_at' => $removedDate,
            ]);

            // Crear transferencia al taller para desmontaje
            \App\Models\Transfer::create([
                'deviceable_type' => Computer::class,
                'deviceable_id' => $computer->id,
                'origin_id' => $location->id,
                'destiny_id' => $workshopLocation?->id ?? $location->id,
                'date' => $removedDate->copy()->subDays(3)->format('Y-m-d'),
                'reason' => 'Traslado para desmontaje y recuperación de componentes',
                'registered_by' => $user->id,
                'status' => 'Finalizado',
                'created_at' => $removedDate->copy()->subDays(3),
                'updated_at' => $removedDate->copy()->subDays(3),
            ]);

            // Crear transferencia de vuelta (sin componentes)
            \App\Models\Transfer::create([
                'deviceable_type' => Computer::class,
                'deviceable_id' => $computer->id,
                'origin_id' => $workshopLocation?->id ?? $location->id,
                'destiny_id' => $location->id,
                'date' => $removedDate->copy()->addDays(1)->format('Y-m-d'),
                'reason' => 'Retorno de equipo desmantelado a ubicación de almacenamiento',
                'registered_by' => $user->id,
                'status' => 'Finalizado',
                'created_at' => $removedDate->copy()->addDays(1),
                'updated_at' => $removedDate->copy()->addDays(1),
            ]);

            // Ahora cambiar a Desmantelado (después de crear el mantenimiento)
            $computer->update(['status' => 'Desmantelado']);

            $this->command->info("   ✓ {$computer->serial} desmantelada (componentes removidos el {$removedDate->format('Y-m-d')})");
        }
    }
}
