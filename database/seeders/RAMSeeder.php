<?php

namespace Database\Seeders;

use App\Models\RAM;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class RAMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rams = [
            ['brand' => 'Corsair', 'model' => 'Vengeance RGB Pro', 'type' => 'DDR5', 'technology' => 'XMP 3.0', 'capacity' => 32, 'frequency' => 6000, 'watts' => 15],
            ['brand' => 'G.Skill', 'model' => 'Trident Z5 RGB', 'type' => 'DDR5', 'technology' => 'XMP 3.0', 'capacity' => 64, 'frequency' => 6400, 'watts' => 18],
            ['brand' => 'G.Skill', 'model' => 'Trident Z5', 'type' => 'DDR5', 'technology' => 'JEDEC', 'capacity' => 32, 'frequency' => 5600, 'watts' => 14],
            ['brand' => 'Kingston', 'model' => 'Fury Beast', 'type' => 'DDR4', 'technology' => 'EXPO', 'capacity' => 32, 'frequency' => 3200, 'watts' => 12],
            ['brand' => 'Kingston', 'model' => 'Fury Beast RGB', 'type' => 'DDR4', 'technology' => 'XMP 2.0', 'capacity' => 16, 'frequency' => 3200, 'watts' => 10],
            ['brand' => 'SK Hynix', 'model' => 'LPDDR5X', 'type' => 'DDR5', 'technology' => 'LPDDR5X', 'capacity' => 16, 'frequency' => 7500, 'watts' => 8],
            ['brand' => 'Samsung', 'model' => 'JEDEC DDR5', 'type' => 'DDR5', 'technology' => 'Standard', 'capacity' => 16, 'frequency' => 4800, 'watts' => 10],
            ['brand' => 'Samsung', 'model' => 'DDR5 Cosmic', 'type' => 'DDR5', 'technology' => 'XMP 3.0', 'capacity' => 48, 'frequency' => 5600, 'watts' => 16],
            ['brand' => 'Micron', 'model' => 'Crucial Ballistix', 'type' => 'DDR4', 'technology' => 'Standard', 'capacity' => 16, 'frequency' => 3600, 'watts' => 10],
            ['brand' => 'Crucial', 'model' => 'Crucial Pro', 'type' => 'DDR5', 'technology' => 'JEDEC', 'capacity' => 32, 'frequency' => 5600, 'watts' => 13],
        ];

        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $provider = Provider::first();

        foreach ($rams as $ramData) {
            $ram = RAM::firstOrCreate($ramData);
            
            for ($i = 0; $i < 2; $i++) {
                Component::create([
                    'componentable_type' => RAM::class,
                    'componentable_id' => $ram->id,
                    'serial' => 'RAM-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => now()->subMonths(rand(1, 24)),
                    'output_date' => null,
                    'status' => collect($statuses)->random(),
                    'warranty_months' => rand(12, 48),
                    'provider_id' => $provider->id,
                ]);
            }
        }

        $this->command->info('✅ Memoria RAM creada correctamente.');
    }
}
