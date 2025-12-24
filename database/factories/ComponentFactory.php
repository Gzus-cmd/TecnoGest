<?php

namespace Database\Factories;

use App\Models\Component;
use App\Models\CPU;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComponentFactory extends Factory
{
    protected $model = Component::class;

    public function definition(): array
    {
        return [
            'serial' => strtoupper(fake()->unique()->bothify('COMP-####-????')),
            'componentable_type' => CPU::class,
            'componentable_id' => CPU::factory(),
            'status' => fake()->randomElement(['Operativo', 'Deficiente', 'Retirado']),
            'provider_id' => Provider::factory(),
            'input_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'warranty_months' => fake()->randomElement([12, 24, 36]),
        ];
    }

    public function operational(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Operativo',
        ]);
    }

    public function deficient(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Deficiente',
        ]);
    }

    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Retirado',
        ]);
    }
}
