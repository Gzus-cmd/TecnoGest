<?php

namespace Database\Factories;

use App\Models\ROM;
use Illuminate\Database\Eloquent\Factories\Factory;

class ROMFactory extends Factory
{
    protected $model = ROM::class;

    public function definition(): array
    {
        return [
            'brand' => fake()->randomElement(['Samsung', 'Western Digital', 'Seagate', 'Kingston', 'Crucial']),
            'model' => fake()->randomElement(['970 EVO Plus', 'SN850X', 'Barracuda', 'A2000', 'P5 Plus']),
            'type' => fake()->randomElement(['SSD NVMe', 'SSD SATA', 'HDD']),
            'capacity' => fake()->randomElement([256, 512, 1000, 2000, 4000]),
            'interface' => fake()->randomElement(['M.2 NVMe', 'SATA III', 'PCIe 4.0']),
            'speed' => fake()->randomElement([550, 3500, 5000, 7000]),
            'watts' => fake()->randomElement([3, 5, 7, 10]),
        ];
    }
}
