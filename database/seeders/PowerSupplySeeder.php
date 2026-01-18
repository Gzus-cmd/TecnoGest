<?php

namespace Database\Seeders;

use App\Models\PowerSupply;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class PowerSupplySeeder extends Seeder
{
    public function run(): void
    {
        $psus = [
            ['brand' => 'Corsair', 'model' => 'RM1000e', 'watts' => 1000, 'certification' => '80+ Gold'],
            ['brand' => 'EVGA', 'model' => 'SuperNOVA 850 GT', 'watts' => 850, 'certification' => '80+ Gold'],
            ['brand' => 'Seasonic', 'model' => 'Focus GX-850', 'watts' => 850, 'certification' => '80+ Gold'],
            ['brand' => 'Thermaltake', 'model' => 'Toughpower GF1 750W', 'watts' => 750, 'certification' => '80+ Gold'],
            ['brand' => 'MSI', 'model' => 'MPG A750GF', 'watts' => 750, 'certification' => '80+ Gold'],
            ['brand' => 'be quiet!', 'model' => 'Straight Power 12', 'watts' => 1000, 'certification' => '80+ Platinum'],
            ['brand' => 'Corsair', 'model' => 'HX1200i', 'watts' => 1200, 'certification' => '80+ Platinum'],
            ['brand' => 'ASUS', 'model' => 'ROG Thor 850P', 'watts' => 850, 'certification' => '80+ Platinum'],
            ['brand' => 'Gigabyte', 'model' => 'P850GM', 'watts' => 850, 'certification' => '80+ Gold'],
            ['brand' => 'Fractal Design', 'model' => 'Ion+ 860P', 'watts' => 860, 'certification' => '80+ Platinum'],
        ];

        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $provider = Provider::first();

        foreach ($psus as $psuData) {
            $psu = PowerSupply::firstOrCreate($psuData);

            // Crear 3 componentes por PSU
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
                    'componentable_type' => 'PowerSupply',
                    'componentable_id' => $psu->id,
                    'serial' => 'PSU-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => $inputDate->toDateString(),
                    'output_date' => $status === 'Retirado' ? $inputDate->addMonths(rand(6, 18))->toDateString() : null,
                    'status' => $status,
                    'warranty_months' => rand(24, 120),
                    'provider_id' => $provider->id,
                ]);
            }
        }

        $this->command->info('✅ Fuentes de Poder (PSU) creadas correctamente.');
    }
}
