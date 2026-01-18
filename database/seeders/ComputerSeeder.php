<?php

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\Location;
use App\Models\OS;
use App\Models\Component;
use App\Models\CPU;
use App\Models\GPU;
use App\Models\RAM;
use App\Models\ROM;
use App\Models\Motherboard;
use App\Models\PowerSupply;
use App\Models\Monitor;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\TowerCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComputerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $normalLocations = Location::where('is_workshop', false)->get();
        $workshopLocation = Location::where('is_workshop', true)->first();
        $operatingSystems = OS::all();

        if ($normalLocations->isEmpty()) {
            $this->command->warn('⚠️ No hay ubicaciones disponibles. Ejecuta LocationSeeder primero.');
            return;
        }

        if ($operatingSystems->isEmpty()) {
            $this->command->warn('⚠️ No hay sistemas operativos disponibles. Ejecuta OSSeeder primero.');
            return;
        }

        // Crear 10 computadoras
        $computersCreated = 0;
        $activeCount = 0;
        $inactiveCount = 0;

        for ($i = 1; $i <= 10; $i++) {
            $os = $operatingSystems->random();

            // Decidir si será Activa o Inactiva
            // 70% Activas, 30% Inactivas
            $isActive = rand(1, 100) <= 70;

            if ($isActive) {
                // PCs ACTIVAS: En ubicaciones normales con periférico
                $location = $normalLocations->random();
                $status = 'Activo';
                $activeCount++;
            } else {
                // PCs INACTIVAS: Solo en talleres SIN periférico
                $location = $workshopLocation ?? $normalLocations->random();
                $status = 'Inactivo';
                $inactiveCount++;
            }

            // Crear la computadora
            $computer = Computer::create([
                'serial' => 'PC-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'location_id' => $location->id,
                'status' => $status,
                'ip_address' => '192.168.' . rand(1, 254) . '.' . rand(1, 254),
                'os_id' => $os->id,
            ]);

            // Asignar componentes internos (CPU) a la computadora
            $this->assignComponents($computer);

            // Si es ACTIVA, crear y asignar periférico
            if ($isActive) {
                $this->createAndAssignPeripheral($computer);
            }

            $computersCreated++;
        }

        $this->command->info("✅ Computadoras creadas: {$computersCreated}");
        $this->command->info("   📍 Activas (con periférico): {$activeCount}");
        $this->command->info("   📍 Inactivas (en taller): {$inactiveCount}");
    }

    /**
     * Crear y asignar periférico a una computadora activa
     */
    private function createAndAssignPeripheral(Computer $computer): void
    {
        // Crear periférico
        $peripheralCount = \App\Models\Peripheral::count();
        $peripheral = \App\Models\Peripheral::create([
            'code' => 'PER-' . str_pad($peripheralCount + 1, 3, '0', STR_PAD_LEFT),
            'location_id' => $computer->location_id,
            'computer_id' => $computer->id,
        ]);

        // Asignar periférico a la computadora
        $computer->update(['peripheral_id' => $peripheral->id]);

        // Asignar componentes al periférico (monitores, teclado, mouse, etc.)
        $this->assignPeripheralComponents($peripheral);
    }

    /**
     * Asignar componentes a una computadora
     */
    private function assignComponents(Computer $computer): void
    {
        // Obtener componentes disponibles (operativos y sin asignar)
        $availableCPUs = Component::where('componentable_type', 'CPU')
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $availableGPUs = Component::where('componentable_type', 'GPU')
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $availableRAMs = Component::where('componentable_type', 'RAM')
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $availableROMs = Component::where('componentable_type', 'ROM')
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $availableMotherboards = Component::where('componentable_type', 'Motherboard')
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $availablePSUs = Component::where('componentable_type', 'PowerSupply')
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        $availableCases = Component::where('componentable_type', 'TowerCase')
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->get();

        // Asignar componentes INTERNOS (CPU) solamente
        $componentsToAttach = [];

        if ($availableCPUs->isNotEmpty()) {
            $componentsToAttach[$availableCPUs->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        if ($availableGPUs->isNotEmpty()) {
            $componentsToAttach[$availableGPUs->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        // 1-2 RAMs
        $ramCount = rand(1, 2);
        for ($i = 0; $i < $ramCount && $availableRAMs->isNotEmpty(); $i++) {
            $ram = $availableRAMs->random();
            $availableRAMs = $availableRAMs->reject(fn($item) => $item->id === $ram->id);
            $componentsToAttach[$ram->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        // 1-2 ROMs
        $romCount = rand(1, 2);
        for ($i = 0; $i < $romCount && $availableROMs->isNotEmpty(); $i++) {
            $rom = $availableROMs->random();
            $availableROMs = $availableROMs->reject(fn($item) => $item->id === $rom->id);
            $componentsToAttach[$rom->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        if ($availableMotherboards->isNotEmpty()) {
            $componentsToAttach[$availableMotherboards->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        if ($availablePSUs->isNotEmpty()) {
            $componentsToAttach[$availablePSUs->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        if ($availableCases->isNotEmpty()) {
            $componentsToAttach[$availableCases->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        // Asignar todos los componentes internos
        $computer->components()->attach($componentsToAttach);
    }

    /**
     * Asignar componentes periféricos (monitores, teclado, mouse, etc.)
     */
    private function assignPeripheralComponents(\App\Models\Peripheral $peripheral): void
    {
        $availableMonitors = Component::where('componentable_type', 'Monitor')
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();

        $availableKeyboards = Component::where('componentable_type', 'Keyboard')
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();

        $availableMice = Component::where('componentable_type', 'Mouse')
            ->where('status', 'Operativo')
            ->whereDoesntHave('peripheral')
            ->get();

        $componentsToAttach = [];

        // 1-2 Monitores
        $monitorCount = rand(1, 2);
        for ($i = 0; $i < $monitorCount && $availableMonitors->isNotEmpty(); $i++) {
            $monitor = $availableMonitors->random();
            $availableMonitors = $availableMonitors->reject(fn($item) => $item->id === $monitor->id);
            $componentsToAttach[$monitor->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        // 1 Teclado
        if ($availableKeyboards->isNotEmpty()) {
            $componentsToAttach[$availableKeyboards->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        // 1 Mouse
        if ($availableMice->isNotEmpty()) {
            $componentsToAttach[$availableMice->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        // Asignar componentes al periférico
        $peripheral->components()->attach($componentsToAttach);
    }
}
