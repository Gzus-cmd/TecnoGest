<?php

namespace Database\Factories;

use App\Models\CPU;
use Illuminate\Database\Eloquent\Factories\Factory;

class CPUFactory extends Factory
{
    protected $model = CPU::class;

    public function definition(): array
    {
        $brands = ['Intel', 'AMD'];
        $brand = fake()->randomElement($brands);
        
        $models = $brand === 'Intel' 
            ? ['Core i3-12100', 'Core i5-13400', 'Core i7-13700K', 'Core i9-13900K', 'Xeon E-2314']
            : ['Ryzen 3 4100', 'Ryzen 5 5600X', 'Ryzen 7 7800X3D', 'Ryzen 9 7950X', 'EPYC 7313'];

        return [
            'brand' => $brand,
            'model' => fake()->randomElement($models),
            'socket' => $brand === 'Intel' ? fake()->randomElement(['LGA1700', 'LGA1200']) : fake()->randomElement(['AM5', 'AM4']),
            'cores' => fake()->randomElement([4, 6, 8, 12, 16, 24]),
            'threads' => fake()->randomElement([8, 12, 16, 24, 32, 48]),
            'architecture' => fake()->randomElement(['x86_64', 'ARM64']),
            'watts' => fake()->randomElement([65, 95, 105, 125, 170]),
        ];
    }
}
