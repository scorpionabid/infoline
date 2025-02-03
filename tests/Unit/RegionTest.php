<?php

namespace Tests\Unit;

use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Application\DTOs\RegionDTO;
use App\Application\Services\RegionService;
use App\Infrastructure\Repositories\RegionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionTest extends TestCase
{
    use RefreshDatabase;

    private RegionService $regionService;
    private RegionRepository $regionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regionRepository = new RegionRepository();
        $this->regionService = new RegionService($this->regionRepository);
    }

    /** @test */
    public function it_can_create_region()
    {
        $data = [
            'name' => 'Bakı şəhəri',
            'phone' => '+994501234567'
        ];

        $dto = new RegionDTO($data);
        $region = $this->regionService->create($dto);

        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($data['name'], $region->name);
        $this->assertEquals($data['phone'], $region->phone);
    }

    /** @test */
    public function it_validates_region_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '', // boş ad
            'phone' => '+994501234567'
        ];

        $dto = new RegionDTO($data);
        $this->regionService->create($dto);
    }

    /** @test */
    public function it_validates_phone_number_format()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => 'Bakı şəhəri',
            'phone' => 'invalid-phone' // yanlış format
        ];

        $dto = new RegionDTO($data);
        $this->regionService->create($dto);
    }

    /** @test */
    public function it_can_update_region()
    {
        // İlk öncə region yaradaq
        $data = [
            'name' => 'Bakı şəhəri',
            'phone' => '+994501234567'
        ];
        $dto = new RegionDTO($data);
        $region = $this->regionService->create($dto);

        // İndi yeniləyək
        $updateData = [
            'name' => 'Bakı',
            'phone' => '+994502345678'
        ];
        $updateDto = new RegionDTO($updateData);
        $updatedRegion = $this->regionService->update($region->id, $updateDto);

        $this->assertEquals($updateData['name'], $updatedRegion->name);
        $this->assertEquals($updateData['phone'], $updatedRegion->phone);
    }

    /** @test */
    public function it_can_delete_region()
    {
        // Region yaradaq
        $data = [
            'name' => 'Bakı şəhəri',
            'phone' => '+994501234567'
        ];
        $dto = new RegionDTO($data);
        $region = $this->regionService->create($dto);

        // Siləndən sonra yoxlayaq
        $this->assertTrue($this->regionService->delete($region->id));
        $this->assertNull($this->regionService->getById($region->id));
    }

    /** @test */
    public function it_has_many_sectors()
    {
        // Region yaradaq
        $data = [
            'name' => 'Bakı şəhəri',
            'phone' => '+994501234567'
        ];
        $dto = new RegionDTO($data);
        $region = $this->regionService->create($dto);

        // İki sektor yaradaq
        Sector::create([
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $region->id
        ]);

        Sector::create([
            'name' => 'Yasamal rayonu',
            'phone' => '+994502345678',
            'region_id' => $region->id
        ]);

        $this->assertCount(2, $region->sectors);
        $this->assertInstanceOf(Sector::class, $region->sectors->first());
    }

    /** @test */
    public function it_cannot_delete_region_with_sectors()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Region yaradaq
        $data = [
            'name' => 'Bakı şəhəri',
            'phone' => '+994501234567'
        ];
        $dto = new RegionDTO($data);
        $region = $this->regionService->create($dto);

        // Sektor yaradaq
        Sector::create([
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $region->id
        ]);

        // Regionu silməyə çalışaq
        $this->regionService->delete($region->id);
    }
}
