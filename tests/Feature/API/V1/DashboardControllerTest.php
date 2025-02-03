<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 'superadmin']);
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_get_statistics()
    {
        $response = $this->getJson('/api/v1/dashboard/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_regions',
                    'total_sectors',
                    'total_schools',
                    'data_submissions' => [
                        'total',
                        'pending',
                        'submitted',
                        'approved'
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function it_can_get_region_statistics()
    {
        $response = $this->getJson('/api/v1/dashboard/region-statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'sectors_count',
                        'schools_count',
                        'data_submissions' => [
                            'total',
                            'approved'
                        ]
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function it_can_get_school_statistics()
    {
        $response = $this->getJson('/api/v1/dashboard/school-statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'sector',
                        'region',
                        'total_submissions',
                        'approved_submissions'
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function it_can_get_data_submission_stats()
    {
        $response = $this->getJson('/api/v1/dashboard/data-submission-stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'submissions_by_status',
                    'submissions_by_month',
                    'latest_submissions'
                ],
                'message'
            ]);
    }
}