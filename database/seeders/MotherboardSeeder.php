<?php

namespace Database\Seeders;

use App\Models\Motherboard;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class MotherboardSeeder extends Seeder
{
    public function run(): void
    {
        $motherboards = [
            ['brand' => 'ASUS', 'model' => 'ROG Maximus Z790-E', 'socket' => 'LGA1700', 'chipset' => 'Z790', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 3, 'watts' => 25],
            ['brand' => 'MSI', 'model' => 'MPG B850E', 'socket' => 'AM5', 'chipset' => 'B850E', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 3, 'watts' => 24],
            ['brand' => 'Gigabyte', 'model' => 'Z790 Aorus Master', 'socket' => 'LGA1700', 'chipset' => 'Z790', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 3, 'watts' => 26],
            ['brand' => 'ASRock', 'model' => 'X870E-E Taichi', 'socket' => 'AM5', 'chipset' => 'X870E', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 4, 'watts' => 27],
            ['brand' => 'ASUS', 'model' => 'ProArt B850-Creator', 'socket' => 'AM5', 'chipset' => 'B850', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 3, 'watts' => 25],
            ['brand' => 'Gigabyte', 'model' => 'B850 Elite AX', 'socket' => 'AM5', 'chipset' => 'B850', 'format' => 'Micro-ATX', 'slots_ram' => 4, 'ports_sata' => 6, 'ports_m2' => 2, 'watts' => 20],
            ['brand' => 'MSI', 'model' => 'MPG Z790I Edge WiFi', 'socket' => 'LGA1700', 'chipset' => 'Z790', 'format' => 'Mini-ITX', 'slots_ram' => 2, 'ports_sata' => 4, 'ports_m2' => 2, 'watts' => 15],
            ['brand' => 'NZXT', 'model' => 'N7 Z590', 'socket' => 'LGA1200', 'chipset' => 'Z590', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 2, 'watts' => 24],
            ['brand' => 'ASUS', 'model' => 'TUF B550-Plus', 'socket' => 'AM4', 'chipset' => 'B550', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 2, 'watts' => 22],
            ['brand' => 'Gigabyte', 'model' => 'X670E Master', 'socket' => 'AM5', 'chipset' => 'X670E', 'format' => 'ATX', 'slots_ram' => 4, 'ports_sata' => 8, 'ports_m2' => 4, 'watts' => 28],
        ];

        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $provider = Provider::first();

        foreach ($motherboards as $mbData) {
            $mb = Motherboard::firstOrCreate($mbData);
            
            // Crear 3 componentes por Motherboard
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
                    'componentable_type' => Motherboard::class,
                    'componentable_id' => $mb->id,
                    'serial' => 'MB-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => $inputDate->toDateString(),
                    'output_date' => $status === 'Retirado' ? $inputDate->addMonths(rand(6, 18))->toDateString() : null,
                    'status' => $status,
                    'warranty_months' => rand(12, 60),
                    'provider_id' => $provider->id,
                ]);
            }
        }

        $this->command->info('✅ Placas Base (Motherboards) creadas correctamente.');
    }
}
