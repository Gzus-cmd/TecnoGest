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
        $locations = Location::where('is_workshop', false)->get();
        $operatingSystems = OS::all();
        
        if ($locations->isEmpty()) {
            $this->command->warn('⚠️ No hay ubicaciones disponibles. Ejecuta LocationSeeder primero.');
            return;
        }

        if ($operatingSystems->isEmpty()) {
            $this->command->warn('⚠️ No hay sistemas operativos disponibles. Ejecuta OSSeeder primero.');
            return;
        }

        // Crear 10 computadoras
        $computersCreated = 0;
        
        for ($i = 1; $i <= 10; $i++) {
            $location = $locations->random();
            $os = $operatingSystems->random();
            
            // Crear la computadora
            $computer = Computer::create([
                'serial' => 'PC-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'location_id' => $location->id,
                'status' => collect(['Activo', 'Inactivo'])->random(),
                'ip_address' => '192.168.' . rand(1, 254) . '.' . rand(1, 254),
                'os_id' => $os->id,
            ]);

            // Asignar componentes a la computadora
            $this->assignComponents($computer);
            
            $computersCreated++;
        }

        $this->command->info("✅ Computadoras creadas: {$computersCreated}");
        $this->command->info("   📍 Distribuidas en " . $locations->count() . " ubicaciones");
    }

    /**
     * Asignar componentes a una computadora
     */
    private function assignComponents(Computer $computer): void
    {
        // Obtener componentes disponibles (operativos y sin asignar)
        $availableCPUs = Component::where('componentable_type', CPU::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableGPUs = Component::where('componentable_type', GPU::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableRAMs = Component::where('componentable_type', RAM::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableROMs = Component::where('componentable_type', ROM::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableMotherboards = Component::where('componentable_type', Motherboard::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availablePSUs = Component::where('componentable_type', PowerSupply::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableMonitors = Component::where('componentable_type', Monitor::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableKeyboards = Component::where('componentable_type', Keyboard::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableMice = Component::where('componentable_type', Mouse::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        $availableCases = Component::where('componentable_type', TowerCase::class)
            ->where('status', 'Operativo')
            ->whereDoesntHave('computers')
            ->whereDoesntHave('printers')
            ->whereDoesntHave('projectors')
            ->get();

        // Asignar 1 componente de cada tipo si está disponible
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

        if ($availableMonitors->isNotEmpty()) {
            $componentsToAttach[$availableMonitors->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        if ($availableKeyboards->isNotEmpty()) {
            $componentsToAttach[$availableKeyboards->random()->id] = [
                'assigned_at' => now(),
                'status' => 'Vigente',
            ];
        }

        if ($availableMice->isNotEmpty()) {
            $componentsToAttach[$availableMice->random()->id] = [
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

        // Asignar todos los componentes
        $computer->components()->attach($componentsToAttach);
    }
}
