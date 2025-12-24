<?php

namespace Tests\Unit\Models;

use App\Models\Computer;
use App\Models\Component;
use App\Models\CPU;
use App\Models\GPU;
use App\Models\RAM;
use App\Models\Location;
use App\Models\OS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComputerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_computer(): void
    {
        $location = Location::factory()->create();
        $os = OS::factory()->create();
        
        $computer = Computer::create([
            'serial' => 'PC-TEST-001',
            'location_id' => $location->id,
            'os_id' => $os->id,
            'status' => 'Activo',
            'ip_address' => '192.168.1.100',
        ]);

        $this->assertDatabaseHas('computers', [
            'serial' => 'PC-TEST-001',
            'status' => 'Activo',
        ]);

        $this->assertInstanceOf(Location::class, $computer->location);
    }

    /** @test */
    public function it_can_have_multiple_components(): void
    {
        $computer = Computer::factory()->create();
        
        $cpu = CPU::factory()->create();
        $cpuComponent = Component::factory()->create([
            'componentable_type' => CPU::class,
            'componentable_id' => $cpu->id,
        ]);
        
        $gpu = GPU::factory()->create();
        $gpuComponent = Component::factory()->create([
            'componentable_type' => GPU::class,
            'componentable_id' => $gpu->id,
        ]);

        $computer->components()->attach($cpuComponent->id, [
            'assigned_at' => now(),
            'status' => 'Vigente',
        ]);
        
        $computer->components()->attach($gpuComponent->id, [
            'assigned_at' => now(),
            'status' => 'Vigente',
        ]);

        $this->assertCount(2, $computer->fresh()->components);
    }

    /** @test */
    public function it_belongs_to_a_location(): void
    {
        $location = Location::factory()->create([
            'name' => 'Oficina Principal',
        ]);
        
        $computer = Computer::factory()->create([
            'location_id' => $location->id,
        ]);

        $this->assertEquals('Oficina Principal', $computer->location->name);
    }

    /** @test */
    public function computer_serial_must_be_unique(): void
    {
        Computer::factory()->create(['serial' => 'UNIQUE-PC-001']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Computer::factory()->create(['serial' => 'UNIQUE-PC-001']);
    }

    /** @test */
    public function it_can_filter_by_status(): void
    {
        Computer::factory()->count(3)->active()->create();
        Computer::factory()->count(2)->inMaintenance()->create();
        Computer::factory()->count(1)->inactive()->create();

        $this->assertEquals(3, Computer::where('status', 'Activo')->count());
        $this->assertEquals(2, Computer::where('status', 'En Mantenimiento')->count());
        $this->assertEquals(1, Computer::where('status', 'Inactivo')->count());
    }
}
