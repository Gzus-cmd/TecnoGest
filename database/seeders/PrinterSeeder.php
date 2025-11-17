<?php

namespace Database\Seeders;

use App\Models\Printer;
use App\Models\PrinterModel;
use App\Models\Location;
use Illuminate\Database\Seeder;

class PrinterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::where('is_workshop', false)->get();
        $printerModels = PrinterModel::all();
        
        if ($locations->isEmpty()) {
            $this->command->warn('⚠️ No hay ubicaciones disponibles. Ejecuta LocationSeeder primero.');
            return;
        }

        if ($printerModels->isEmpty()) {
            $this->command->warn('⚠️ No hay modelos de impresoras disponibles. Ejecuta PrinterModelSeeder primero.');
            return;
        }

        // Crear 8 impresoras
        $printersCreated = 0;
        
        for ($i = 1; $i <= 8; $i++) {
            $location = $locations->random();
            $model = $printerModels->random();
            
            Printer::create([
                'modelo_id' => $model->id,
                'serial' => 'PRINTER-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'location_id' => $location->id,
                'ip_address' => '192.168.' . rand(1, 254) . '.' . rand(1, 254),
                'status' => collect(['Activo', 'Inactivo', 'En Mantenimiento'])->random(),
                'warranty_months' => rand(12, 36),
                'input_date' => now()->subMonths(rand(1, 24))->toDateString(),
                'output_date' => null,
            ]);
            
            $printersCreated++;
        }

        $this->command->info("✅ Impresoras creadas: {$printersCreated}");
        $this->command->info("   📍 Distribuidas en " . $locations->count() . " ubicaciones");
        $this->command->info("   🖨️  Usando " . $printerModels->count() . " modelos diferentes");
    }
}
