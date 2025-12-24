<?php

namespace Database\Factories;

use App\Models\OS;
use Illuminate\Database\Eloquent\Factories\Factory;

class OSFactory extends Factory
{
    protected $model = OS::class;

    public function definition(): array
    {
        $systems = [
            ['name' => 'Windows', 'version' => '11 Pro', 'architecture' => 'x64'],
            ['name' => 'Windows', 'version' => '10 Pro', 'architecture' => 'x64'],
            ['name' => 'Windows', 'version' => '10 Home', 'architecture' => 'x64'],
            ['name' => 'Ubuntu', 'version' => '24.04 LTS', 'architecture' => 'x64'],
            ['name' => 'Ubuntu', 'version' => '22.04 LTS', 'architecture' => 'x64'],
            ['name' => 'macOS', 'version' => 'Sonoma 14', 'architecture' => 'ARM64'],
        ];

        $selected = fake()->randomElement($systems);

        return [
            'name' => $selected['name'],
            'version' => $selected['version'],
            'architecture' => $selected['architecture'],
        ];
    }

    public function windows(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Windows',
            'version' => '11 Pro',
            'architecture' => 'x64',
        ]);
    }

    public function ubuntu(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ubuntu',
            'version' => '24.04 LTS',
            'architecture' => 'x64',
        ]);
    }
}
