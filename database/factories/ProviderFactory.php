<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        return [
            'ruc' => fake()->numerify('###########'),
            'name' => fake()->company(),
            'phone' => fake()->numerify('#########'),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'status' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}
