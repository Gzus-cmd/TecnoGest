<?php

namespace Database\Seeders;

use App\Models\Projector;
use App\Models\ProjectorModel;
use App\Models\Location;
use Illuminate\Database\Seeder;

class ProjectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::where('is_workshop', false)->get();
        $projectorModels = ProjectorModel::all();
        
        if ($locations->isEmpty()) {
            $this->command->warn('⚠️ No hay ubicaciones disponibles. Ejecuta LocationSeeder primero.');
            return;
        }

        if ($projectorModels->isEmpty()) {
            $this->command->warn('⚠️ No hay modelos de proyectores disponibles. Ejecuta ProjectorModelSeeder primero.');
            return;
        }

        // Crear 6 proyectores
        $projectorsCreated = 0;
        
        for ($i = 1; $i <= 6; $i++) {
            $location = $locations->random();
            $model = $projectorModels->random();
            
            Projector::create([
                'modelo_id' => $model->id,
                'serial' => 'PROJ-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'location_id' => $location->id,
                'status' => collect(['Activo', 'Inactivo', 'En Mantenimiento'])->random(),
                'warranty_months' => rand(12, 36),
                'input_date' => now()->subMonths(rand(1, 24))->toDateString(),
                'output_date' => null,
            ]);
            
            $projectorsCreated++;
        }

        $this->command->info("✅ Proyectores creados: {$projectorsCreated}");
        $this->command->info("   📍 Distribuidos en " . $locations->count() . " ubicaciones");
        $this->command->info("   📽️  Usando " . $projectorModels->count() . " modelos diferentes");
    }
}
