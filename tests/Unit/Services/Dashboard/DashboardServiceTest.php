<?php

namespace Tests\Unit\Services\Dashboard;

use App\Domain\Entities\DataValue;
use App\Domain\Entities\Region;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Services\Dashboard\DashboardService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
    }

    /** @test */
    public function it_can_get_general_statistics()
    {
        // Test datası yaradaq
        Region::factory()->count(3)->create();
        Sector::factory()->count(5)->create();
        School::factory()->count(10)->create();
        DataValue::factory()->count(20)->create(['status' => 'draft']);
        DataValue::factory()->count(15)->create(['status' => 'submitted']);
        DataValue::factory()->count(10)->create(['status' => 'approved']);

        $stats = $this->service->getStatistics();

        $this->assertEquals(3, $stats['total_regions']);
        $this->assertEquals(5, $stats['total_sectors']);
        $this->assertEquals(10, $stats['total_schools']);
        $this->assertEquals(45, $stats['data_submissions']['total']);
        $this->assertEquals(20, $stats['data_submissions']['pending']);
        $this->assertEquals(15, $stats['data_submissions']['submitted']);
        $this->assertEquals(10, $stats['data_submissions']['approved']);
    }

    /** @test */
    public function it_can_get_region_statistics()
    {
        $region = Region::factory()->create();
        Sector::factory()->count(2)->create(['region_id' => $region->id]);
        School::factory()->count(5)->create(['sector_id' => 1]);
        
        $stats = $this->service->getRegionStatistics();
        
        $this->assertCount(1, $stats);
        $this->assertEquals($region->id, $stats[0]['id']);
        $this->assertEquals($region->name, $stats[0]['name']);
        $this->assertEquals(2, $stats[0]['sectors_count']);
        $this->assertEquals(5, $stats[0]['schools_count']);
    }

    /** @test */
    public function it_can_get_school_statistics()
    {
        $region = Region::factory()->create();
        $sector = Sector::factory()->create(['region_id' => $region->id]);
        $school = School::factory()->create(['sector_id' => $sector->id]);
        
        DataValue::factory()->count(5)->create([
            'school_id' => $school->id,
            'status' => 'approved'
        ]);
        
        $stats = $this->service->getSchoolStatistics($region->id, $sector->id);
        
        $this->assertCount(1, $stats);
        $this->assertEquals($school->id, $stats[0]['id']);
        $this->assertEquals($school->name, $stats[0]['name']);
        $this->assertEquals(5, $stats[0]['total_submissions']);
        $this->assertEquals(5, $stats[0]['approved_submissions']);
    }

    /** @test */
    public function it_can_get_data_submission_stats()
    {
        DataValue::factory()->count(10)->create(['status' => 'draft']);
        DataValue::factory()->count(5)->create(['status' => 'approved']);
        
        $stats = $this->service->getDataSubmissionStats();
        
        $this->assertArrayHasKey('submissions_by_status', $stats);
        $this->assertArrayHasKey('submissions_by_month', $stats);
        $this->assertArrayHasKey('latest_submissions', $stats);
        
        $this->assertEquals(10, $stats['submissions_by_status']['draft']);
        $this->assertEquals(5, $stats['submissions_by_status']['approved']);
    }
}
