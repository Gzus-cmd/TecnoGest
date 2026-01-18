<?php

namespace Database\Seeders;

use App\Models\ROM;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class ROMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roms = [
            ['brand' => 'Samsung', 'model' => '990 Pro', 'type' => 'NVMe', 'capacity' => 4000, 'interface' => 'PCIe 4.0', 'speed' => 7100, 'watts' => 8],
            ['brand' => 'Samsung', 'model' => '980 Pro', 'type' => 'NVMe', 'capacity' => 2000, 'interface' => 'PCIe 4.0', 'speed' => 7000, 'watts' => 7],
            ['brand' => 'Western Digital', 'model' => 'Black SN850X', 'type' => 'NVMe', 'capacity' => 2000, 'interface' => 'PCIe 4.0', 'speed' => 7100, 'watts' => 7],
            ['brand' => 'Western Digital', 'model' => 'Blue SN580', 'type' => 'NVMe', 'capacity' => 1000, 'interface' => 'PCIe 4.0', 'speed' => 6000, 'watts' => 6],
            ['brand' => 'SK Hynix', 'model' => 'P41 Platinum', 'type' => 'NVMe', 'capacity' => 1000, 'interface' => 'PCIe 4.0', 'speed' => 6800, 'watts' => 6],
            ['brand' => 'Crucial', 'model' => 'P5 Plus', 'type' => 'NVMe', 'capacity' => 2000, 'interface' => 'PCIe 4.0', 'speed' => 6600, 'watts' => 7],
            ['brand' => 'Crucial', 'model' => 'MX500', 'type' => 'SSD SATA', 'capacity' => 1000, 'interface' => 'SATA', 'speed' => 560, 'watts' => 5],
            ['brand' => 'Kingston', 'model' => 'NV2', 'type' => 'NVMe', 'capacity' => 1000, 'interface' => 'PCIe 3.0', 'speed' => 3500, 'watts' => 5],
            ['brand' => 'Seagate', 'model' => 'Barracuda Pro', 'type' => 'HDD', 'capacity' => 12000, 'interface' => 'SATA', 'speed' => 272, 'watts' => 10],
            ['brand' => 'Western Digital', 'model' => 'Red Pro', 'type' => 'HDD', 'capacity' => 8000, 'interface' => 'SATA', 'speed' => 272, 'watts' => 9],
        ];

        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $provider = Provider::first();

        foreach ($roms as $romData) {
            $rom = ROM::firstOrCreate($romData);

            // Crear 3 componentes por ROM
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
                    'componentable_type' => 'ROM',
                    'componentable_id' => $rom->id,
                    'serial' => 'ROM-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => $inputDate->toDateString(),
                    'output_date' => $status === 'Retirado' ? $inputDate->addMonths(rand(6, 18))->toDateString() : null,
                    'status' => $status,
                    'warranty_months' => rand(24, 60),
                    'provider_id' => $provider->id,
                ]);
            }
        }

        $this->command->info('✅ Almacenamiento (ROM) creado correctamente.');
    }
}
