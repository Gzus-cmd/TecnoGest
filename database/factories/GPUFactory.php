<?php

namespace Database\Factories;

use App\Models\GPU;
use Illuminate\Database\Eloquent\Factories\Factory;

class GPUFactory extends Factory
{
    protected $model = GPU::class;

    public function definition(): array
    {
        $brands = ['NVIDIA', 'AMD', 'Intel'];
        $brand = fake()->randomElement($brands);
        
        $models = match($brand) {
            'NVIDIA' => ['GeForce RTX 4090', 'GeForce RTX 4080', 'GeForce RTX 4070', 'GeForce RTX 3060', 'Quadro P2200'],
            'AMD' => ['Radeon RX 7900 XTX', 'Radeon RX 7800 XT', 'Radeon RX 6700 XT', 'Radeon Pro W6600'],
            'Intel' => ['Arc A770', 'Arc A750', 'Arc A380'],
        };

        return [
            'brand' => $brand,
            'model' => fake()->randomElement($models),
            'memory' => fake()->randomElement(['GDDR6', 'GDDR6X']),
            'capacity' => fake()->randomElement([4, 6, 8, 12, 16, 24]),
            'interface' => 'PCIe 4.0 x16',
            'frequency' => fake()->numberBetween(1500, 2500),
            'watts' => fake()->randomElement([75, 130, 170, 225, 320, 450]),
        ];
    }
}
