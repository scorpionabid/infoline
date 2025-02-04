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
    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
        $this->cleanupTestData();
    }

    private function cleanupTestData(): void
    {
        Region::query()->forceDelete();
        Sector::query()->forceDelete();
        School::query()->forceDelete();
        DataValue::query()->forceDelete();
    }

    /** @test */
    public function it_can_get_general_statistics()
    {
        // Arrange
        $this->createTestData();

        // Act
        $stats = $this->service->getStatistics();

        // Assert
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
        // Arrange
        $this->createBasicRegionData();

        // Act
        $stats = $this->service->getRegionStatistics();

        // Assert
        $this->assertCount(1, $stats);
        $firstRegion = $stats[0];
        $this->assertEquals($this->region->id, $firstRegion['id']);
        $this->assertEquals($this->region->name, $firstRegion['name']);
        $this->assertEquals(2, $firstRegion['sectors_count']);
        $this->assertEquals(4, $firstRegion['schools_count']);
    }

    /** @test */
    public function it_can_get_school_statistics()
    {
        // Arrange
        $this->createBasicRegionData();
        $sector = Sector::first();
        
        // Act
        $stats = $this->service->getSchoolStatistics($this->region->id, $sector->id);

        // Assert
        $this->assertNotEmpty($stats);
        $this->assertCount(2, $stats);
        $this->assertEquals($sector->name, $stats[0]['sector']);
        $this->assertEquals($this->region->name, $stats[0]['region']);
    }

    /** @test */
    public function it_can_get_data_submission_stats()
    {
        // Arrange
        $this->createSubmissionData();

        // Act
        $stats = $this->service->getDataSubmissionStats();

        // Assert
        $this->assertArrayHasKey('submissions_by_status', $stats);
        $this->assertArrayHasKey('submissions_by_month', $stats);
        $this->assertArrayHasKey('latest_submissions', $stats);
        $this->assertEquals(20, $stats['submissions_by_status']['draft']);
        $this->assertEquals(10, $stats['submissions_by_status']['approved']);
    }

    private function createTestData(): void
    {
        // Məhdud sayda region yaradın
        $this->region = Region::factory()->create();
        Region::factory()->count(2)->create();

        // Birinci regiona 5 sektor əlavə edin
        $sectors = Sector::factory()
            ->count(5)
            ->create(['region_id' => $this->region->id]);

        // Hər sektora 2 məktəb əlavə edin
        foreach ($sectors as $sector) {
            School::factory()
                ->count(2)
                ->create(['sector_id' => $sector->id]);
        }

        // Təlimatlara uyğun data submission yaradın
        DataValue::factory()->count(20)->create(['status' => 'draft']);
        DataValue::factory()->count(15)->create(['status' => 'submitted']);
        DataValue::factory()->count(10)->create(['status' => 'approved']);
    }

    private function createBasicRegionData(): void
    {
        $this->region = Region::factory()->create();
        
        $sectors = Sector::factory()
            ->count(2)
            ->create(['region_id' => $this->region->id]);

        foreach ($sectors as $sector) {
            School::factory()
                ->count(2)
                ->create(['sector_id' => $sector->id]);
        }
    }

    private function createSubmissionData(): void
    {
        $this->createBasicRegionData();
        
        DataValue::factory()->count(20)->create(['status' => 'draft']);
        DataValue::factory()->count(10)->create(['status' => 'approved']);
    }
}