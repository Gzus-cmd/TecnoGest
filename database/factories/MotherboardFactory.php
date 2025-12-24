<?php

namespace Database\Factories;

use App\Models\Motherboard;
use Illuminate\Database\Eloquent\Factories\Factory;

class MotherboardFactory extends Factory
{
    protected $model = Motherboard::class;

    public function definition(): array
    {
        return [
            'brand' => fake()->randomElement(['ASUS', 'Gigabyte', 'MSI', 'ASRock']),
            'model' => fake()->randomElement(['ROG STRIX Z790-E', 'AORUS MASTER', 'MEG Z790 ACE', 'Taichi X670E']),
            'socket' => fake()->randomElement(['LGA1700', 'AM5', 'LGA1200', 'AM4']),
            'chipset' => fake()->randomElement(['Z790', 'X670E', 'B660', 'B550']),
            'form_factor' => fake()->randomElement(['ATX', 'Micro-ATX', 'Mini-ITX']),
            'memory_type' => fake()->randomElement(['DDR4', 'DDR5']),
            'memory_slots' => fake()->randomElement([2, 4]),
            'max_memory' => fake()->randomElement([64, 128, 192]),
            'watts' => fake()->randomElement([30, 50, 70]),
        ];
    }
}
