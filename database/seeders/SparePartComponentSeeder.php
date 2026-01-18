<?php

namespace Database\Seeders;

use App\Models\SparePart;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class SparePartComponentSeeder extends Seeder
{
    /**
     * Run the database seeds - Crear instancias de repuestos en Component
     */
    public function run(): void
    {
        $spareParts = SparePart::all();
        $provider = Provider::first();

        if ($spareParts->isEmpty()) {
            $this->command->warn('⚠️ No hay repuestos en el catálogo. Ejecuta SparePartSeeder primero.');
            return;
        }

        if (!$provider) {
            $this->command->warn('⚠️ No hay proveedores disponibles. Ejecuta ProviderSeeder primero.');
            return;
        }

        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $instancesCreated = 0;

        // Crear 1-2 instancias por cada modelo de repuesto
        foreach ($spareParts as $sparePart) {
            $instanceCount = rand(1, 2);

            for ($i = 0; $i < $instanceCount; $i++) {
                $status = collect($statuses)->random();
                $inputDate = now()->subMonths(rand(1, 24));

                Component::create([
                    'componentable_type' => 'SparePart',
                    'componentable_id' => $sparePart->id,
                    'serial' => 'SPARE-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => $inputDate->toDateString(),
                    'output_date' => $status === 'Retirado' ? $inputDate->addMonths(rand(6, 18))->toDateString() : null,
                    'status' => $status,
                    'warranty_months' => rand(12, 36),
                    'provider_id' => $provider->id,
                ]);

                $instancesCreated++;
            }
        }

        $this->command->info("✅ Instancias de repuestos creadas: {$instancesCreated}");
        $this->command->info("   📦 Basadas en " . $spareParts->count() . " modelos del catálogo");
    }
}
