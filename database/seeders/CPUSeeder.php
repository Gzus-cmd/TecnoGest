<?php

namespace Database\Seeders;

use App\Models\CPU;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class CPUSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cpus = [
            ['brand' => 'Intel', 'model' => 'Core i9-13900K', 'socket' => 'LGA1700', 'cores' => 24, 'threads' => 32, 'architecture' => 'Raptor Lake', 'watts' => 253],
            ['brand' => 'Intel', 'model' => 'Core i7-13700K', 'socket' => 'LGA1700', 'cores' => 16, 'threads' => 24, 'architecture' => 'Raptor Lake', 'watts' => 253],
            ['brand' => 'Intel', 'model' => 'Core i5-13600K', 'socket' => 'LGA1700', 'cores' => 14, 'threads' => 20, 'architecture' => 'Raptor Lake', 'watts' => 181],
            ['brand' => 'Intel', 'model' => 'Core i3-13100', 'socket' => 'LGA1700', 'cores' => 4, 'threads' => 8, 'architecture' => 'Raptor Lake', 'watts' => 60],
            ['brand' => 'AMD', 'model' => 'Ryzen 9 7950X', 'socket' => 'AM5', 'cores' => 16, 'threads' => 32, 'architecture' => 'Zen 4', 'watts' => 170],
            ['brand' => 'AMD', 'model' => 'Ryzen 7 7700X', 'socket' => 'AM5', 'cores' => 8, 'threads' => 16, 'architecture' => 'Zen 4', 'watts' => 105],
            ['brand' => 'AMD', 'model' => 'Ryzen 5 5600X', 'socket' => 'AM4', 'cores' => 6, 'threads' => 12, 'architecture' => 'Zen 3', 'watts' => 65],
            ['brand' => 'Intel', 'model' => 'Xeon W9-3495X', 'socket' => 'TBG', 'cores' => 60, 'threads' => 120, 'architecture' => 'Sapphire Rapids', 'watts' => 350],
            ['brand' => 'AMD', 'model' => 'Ryzen Threadripper PRO 5995WX', 'socket' => 'TRX4', 'cores' => 64, 'threads' => 128, 'architecture' => 'Zen 3', 'watts' => 280],
            ['brand' => 'Intel', 'model' => 'Core Ultra 9 285K', 'socket' => 'LGA1851', 'cores' => 24, 'threads' => 24, 'architecture' => 'Arrow Lake', 'watts' => 125],
            ['brand' => 'ARM', 'model' => 'Apple M3 Max', 'socket' => 'Integrated', 'cores' => 12, 'threads' => 12, 'architecture' => 'ARM', 'watts' => 30],
            ['brand' => 'Qualcomm', 'model' => 'Snapdragon 8 Gen 3', 'socket' => 'Integrated', 'cores' => 8, 'threads' => 8, 'architecture' => 'ARM', 'watts' => 15],
        ];

        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $provider = Provider::first();

        foreach ($cpus as $cpuData) {
            $cpu = CPU::firstOrCreate($cpuData);

            // Crear 3 componentes por CPU (para tener más disponibles)
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
                    'componentable_type' => 'CPU',
                    'componentable_id' => $cpu->id,
                    'serial' => 'CPU-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => $inputDate->toDateString(),
                    'output_date' => $status === 'Retirado' ? $inputDate->addMonths(rand(6, 18))->toDateString() : null,
                    'status' => $status,
                    'warranty_months' => rand(12, 60),
                    'provider_id' => $provider->id,
                ]);
            }
        }

        $this->command->info('✅ Procesadores (CPUs) creados correctamente.');
    }
}
