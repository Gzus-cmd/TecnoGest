<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $pavilions = ['Pabellón A', 'Pabellón B', 'Pabellón C', 'Pabellón D'];
        
        return [
            'pavilion' => fake()->randomElement($pavilions),
            'apartment' => fake()->numberBetween(100, 500),
            'name' => 'Oficina ' . fake()->numberBetween(1, 20),
            'is_workshop' => false,
        ];
    }

    public function workshop(): static
    {
        return $this->state(fn (array $attributes) => [
            'pavilion' => 'Principal',
            'apartment' => 1,
            'name' => 'Taller de Mantenimiento',
            'is_workshop' => true,
        ]);
    }
}
