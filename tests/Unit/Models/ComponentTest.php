<?php

namespace Tests\Unit\Models;

use App\Models\Component;
use App\Models\CPU;
use App\Models\GPU;
use App\Models\RAM;
use App\Models\ROM;
use App\Models\Motherboard;
use App\Models\Computer;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentTest extends TestCase
{
    use RefreshDatabase;

    protected Provider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = Provider::factory()->create();
    }

    /** @test */
    public function it_can_create_a_component_with_cpu(): void
    {
        $cpu = CPU::factory()->create([
            'brand' => 'Intel',
            'model' => 'Core i7-13700K',
        ]);

        $component = Component::create([
            'serial' => 'CPU-TEST-001',
            'componentable_type' => CPU::class,
            'componentable_id' => $cpu->id,
            'status' => 'Operativo',
            'provider_id' => $this->provider->id,
        ]);

        $this->assertDatabaseHas('components', [
            'serial' => 'CPU-TEST-001',
            'componentable_type' => CPU::class,
            'componentable_id' => $cpu->id,
        ]);

        $this->assertInstanceOf(CPU::class, $component->componentable);
        $this->assertEquals('Intel', $component->componentable->brand);
    }

    /** @test */
    public function it_can_create_a_component_with_gpu(): void
    {
        $gpu = GPU::factory()->create([
            'brand' => 'NVIDIA',
            'model' => 'RTX 4090',
        ]);

        $component = Component::create([
            'serial' => 'GPU-TEST-001',
            'componentable_type' => GPU::class,
            'componentable_id' => $gpu->id,
            'status' => 'Operativo',
            'provider_id' => $this->provider->id,
        ]);

        $this->assertInstanceOf(GPU::class, $component->componentable);
        $this->assertEquals('NVIDIA', $component->componentable->brand);
    }

    /** @test */
    public function it_can_create_a_component_with_ram(): void
    {
        $ram = RAM::factory()->create([
            'brand' => 'Corsair',
            'model' => 'Vengeance DDR5',
            'capacity' => 32,
        ]);

        $component = Component::create([
            'serial' => 'RAM-TEST-001',
            'componentable_type' => RAM::class,
            'componentable_id' => $ram->id,
            'status' => 'Operativo',
            'provider_id' => $this->provider->id,
        ]);

        $this->assertInstanceOf(RAM::class, $component->componentable);
        $this->assertEquals(32, $component->componentable->capacity);
    }

    /** @test */
    public function it_has_correct_status_values(): void
    {
        $cpu = CPU::factory()->create();

        $component = Component::create([
            'serial' => 'CPU-STATUS-001',
            'componentable_type' => CPU::class,
            'componentable_id' => $cpu->id,
            'status' => 'Operativo',
            'provider_id' => $this->provider->id,
        ]);

        $this->assertEquals('Operativo', $component->status);

        $component->update(['status' => 'Deficiente']);
        $this->assertEquals('Deficiente', $component->fresh()->status);

        $component->update(['status' => 'Retirado']);
        $this->assertEquals('Retirado', $component->fresh()->status);
    }

    /** @test */
    public function it_can_be_assigned_to_a_computer(): void
    {
        $cpu = CPU::factory()->create();
        $component = Component::factory()->create([
            'componentable_type' => CPU::class,
            'componentable_id' => $cpu->id,
        ]);
        
        $computer = Computer::factory()->create();

        // Asignar componente a computadora
        $computer->components()->attach($component->id, [
            'assigned_at' => now(),
            'status' => 'Vigente',
        ]);

        $this->assertTrue($computer->components->contains($component));
        $this->assertCount(1, $computer->components);
    }

    /** @test */
    public function component_serial_must_be_unique(): void
    {
        $cpu1 = CPU::factory()->create();
        $cpu2 = CPU::factory()->create();

        Component::create([
            'serial' => 'UNIQUE-SERIAL-001',
            'componentable_type' => CPU::class,
            'componentable_id' => $cpu1->id,
            'status' => 'Operativo',
            'provider_id' => $this->provider->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Component::create([
            'serial' => 'UNIQUE-SERIAL-001', // Mismo serial
            'componentable_type' => CPU::class,
            'componentable_id' => $cpu2->id,
            'status' => 'Operativo',
            'provider_id' => $this->provider->id,
        ]);
    }
}
