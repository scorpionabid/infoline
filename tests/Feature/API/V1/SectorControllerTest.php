<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Sector;
use App\Domain\Entities\Region;
use Tests\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectorControllerTest extends ApiTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->region = Region::factory()->create();
    }

    /** @test */
    public function it_can_create_sector()
    {
        $response = $this->actingAsUser()
            ->postJson('/api/v1/sectors', [
                'name' => 'Test Sector',
                'region_id' => $this->region->id,
                'phone' => '+994501234567'
            ]);

        $response->assertCreated();
    }
}