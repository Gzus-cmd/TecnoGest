<?php

namespace Tests\Unit\Models;

use App\Models\Location;
use App\Models\Computer;
use App\Models\Transfer;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_location(): void
    {
        $location = Location::create([
            'pavilion' => 'Pabellón A',
            'apartment' => 101,
            'name' => 'Oficina 101',
            'is_workshop' => false,
        ]);

        $this->assertDatabaseHas('locations', [
            'pavilion' => 'Pabellón A',
            'apartment' => 101,
            'name' => 'Oficina 101',
            'is_workshop' => false,
        ]);
    }

    /** @test */
    public function it_can_be_a_workshop(): void
    {
        $workshop = Location::factory()->workshop()->create();

        $this->assertTrue($workshop->is_workshop);
    }

    /** @test */
    public function it_can_have_computers(): void
    {
        $location = Location::factory()->create();
        
        Computer::factory()->count(3)->create([
            'location_id' => $location->id,
        ]);

        $this->assertCount(3, $location->fresh()->computers);
    }

    /** @test */
    public function workshop_can_be_identified(): void
    {
        Location::factory()->create(['is_workshop' => false]);
        Location::factory()->create(['is_workshop' => false]);
        $workshop = Location::factory()->workshop()->create();

        $workshops = Location::where('is_workshop', true)->get();
        
        $this->assertCount(1, $workshops);
        $this->assertEquals($workshop->id, $workshops->first()->id);
    }
}
