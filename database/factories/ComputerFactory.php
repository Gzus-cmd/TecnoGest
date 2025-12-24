<?php

namespace Database\Factories;

use App\Models\Computer;
use App\Models\Location;
use App\Models\OS;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComputerFactory extends Factory
{
    protected $model = Computer::class;

    public function definition(): array
    {
        return [
            'serial' => strtoupper(fake()->unique()->bothify('PC-####-????')),
            'location_id' => Location::factory(),
            'status' => fake()->randomElement(['Activo', 'Inactivo', 'En Mantenimiento', 'Desmantelado']),
            'ip_address' => fake()->ipv4(),
            'os_id' => OS::factory(),
            'peripheral_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Activo',
        ]);
    }

    public function inMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'En Mantenimiento',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Inactivo',
        ]);
    }

    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Desmantelado',
        ]);
    }
}
