<?php

namespace Database\Seeders;

use App\Models\GPU;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class GPUSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gpus = [
            ['brand' => 'NVIDIA', 'model' => 'RTX 4090', 'memory' => 'GDDR6X', 'capacity' => 24, 'interface' => 'PCIe 4.0', 'frequency' => 2505, 'watts' => 450],
            ['brand' => 'NVIDIA', 'model' => 'RTX 4080', 'memory' => 'GDDR6X', 'capacity' => 16, 'interface' => 'PCIe 4.0', 'frequency' => 2505, 'watts' => 320],
            ['brand' => 'NVIDIA', 'model' => 'RTX 4070', 'memory' => 'GDDR6', 'capacity' => 12, 'interface' => 'PCIe 4.0', 'frequency' => 2475, 'watts' => 200],
            ['brand' => 'NVIDIA', 'model' => 'RTX 4060', 'memory' => 'GDDR6', 'capacity' => 8, 'interface' => 'PCIe 4.0', 'frequency' => 2505, 'watts' => 115],
            ['brand' => 'AMD', 'model' => 'Radeon RX 7900 XTX', 'memory' => 'GDDR6', 'capacity' => 24, 'interface' => 'PCIe 4.0', 'frequency' => 2500, 'watts' => 420],
            ['brand' => 'AMD', 'model' => 'Radeon RX 7900 XT', 'memory' => 'GDDR6', 'capacity' => 20, 'interface' => 'PCIe 4.0', 'frequency' => 2405, 'watts' => 370],
            ['brand' => 'AMD', 'model' => 'Radeon RX 7800 XT', 'memory' => 'GDDR6', 'capacity' => 16, 'interface' => 'PCIe 4.0', 'frequency' => 2430, 'watts' => 310],
            ['brand' => 'Intel', 'model' => 'Arc A770', 'memory' => 'GDDR6', 'capacity' => 16, 'interface' => 'PCIe 4.0', 'frequency' => 2550, 'watts' => 225],
            ['brand' => 'Intel', 'model' => 'Arc A750', 'memory' => 'GDDR6', 'capacity' => 8, 'interface' => 'PCIe 4.0', 'frequency' => 2350, 'watts' => 130],
        ];

        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $provider = Provider::first();

        foreach ($gpus as $gpuData) {
            $gpu = GPU::firstOrCreate($gpuData);
            
            // Crear 3 componentes por GPU
            for ($i = 0; $i < 3; $i++) {
                // 60% Operativo, 20% Deficiente, 20% Retirado
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    $status = 'Operativo';
                } elseif ($rand <= 80) {
                    $status = 'Deficiente';
                } else {
                    $status = 'Retirado';
                }
                
                $inputDate = now()->subMonths(rand(1, 24));

                Component::create([
                    'componentable_type' => GPU::class,
                    'componentable_id' => $gpu->id,
                    'serial' => 'GPU-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => $inputDate->toDateString(),
                    'output_date' => $status === 'Retirado' ? $inputDate->addMonths(rand(6, 18))->toDateString() : null,
                    'status' => $status,
                    'warranty_months' => rand(12, 36),
                    'provider_id' => $provider->id,
                ]);
            }
        }

        $this->command->info('✅ Tarjetas Gráficas (GPUs) creadas correctamente.');
    }
}
