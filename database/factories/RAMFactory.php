<?php

namespace Database\Factories;

use App\Models\RAM;
use Illuminate\Database\Eloquent\Factories\Factory;

class RAMFactory extends Factory
{
    protected $model = RAM::class;

    public function definition(): array
    {
        return [
            'brand' => fake()->randomElement(['Corsair', 'G.Skill', 'Kingston', 'Crucial', 'TeamGroup']),
            'model' => fake()->randomElement(['Vengeance LPX', 'Trident Z5', 'Fury Beast', 'Ballistix', 'T-Force Delta']),
            'type' => fake()->randomElement(['DDR4', 'DDR5']),
            'technology' => fake()->randomElement(['DIMM', 'SO-DIMM']),
            'capacity' => fake()->randomElement([8, 16, 32, 64]),
            'frequency' => fake()->randomElement([3200, 3600, 4800, 5600, 6000]),
            'watts' => fake()->randomElement([1, 2, 3, 5]),
        ];
    }
}
