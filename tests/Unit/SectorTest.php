<?php

namespace Tests\Unit;

use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Application\DTOs\SectorDTO;
use App\Application\Services\SectorService;
use App\Infrastructure\Repositories\SectorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectorTest extends TestCase
{
    use RefreshDatabase;

    private SectorService $sectorService;
    private SectorRepository $sectorRepository;
    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectorRepository = new SectorRepository();
        $this->sectorService = new SectorService($this->sectorRepository);

        // Test üçün region yaradaq
        $this->region = Region::create([
            'name' => 'Bakı şəhəri',
            'phone' => '+994501234567'
        ]);
    }

    /** @test */
    public function it_can_create_sector()
    {
        $data = [
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $this->region->id
        ];

        $dto = new SectorDTO($data);
        $sector = $this->sectorService->create($dto);

        $this->assertInstanceOf(Sector::class, $sector);
        $this->assertEquals($data['name'], $sector->name);
        $this->assertEquals($data['phone'], $sector->phone);
        $this->assertEquals($data['region_id'], $sector->region_id);
    }

    /** @test */
    public function it_validates_sector_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '', // boş ad
            'phone' => '+994501234567',
            'region_id' => $this->region->id
        ];

        $dto = new SectorDTO($data);
        $this->sectorService->create($dto);
    }

    /** @test */
    public function it_validates_phone_format()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => 'Binəqədi rayonu',
            'phone' => 'invalid-phone', // yanlış format
            'region_id' => $this->region->id
        ];

        $dto = new SectorDTO($data);
        $this->sectorService->create($dto);
    }

    /** @test */
    public function it_validates_region_existence()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => 999 // mövcud olmayan region
        ];

        $dto = new SectorDTO($data);
        $this->sectorService->create($dto);
    }

    /** @test */
    public function it_can_update_sector()
    {
        // İlk öncə sektor yaradaq
        $data = [
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $this->region->id
        ];
        $dto = new SectorDTO($data);
        $sector = $this->sectorService->create($dto);

        // İndi yeniləyək
        $updateData = [
            'name' => 'Binəqədi',
            'phone' => '+994502345678',
            'region_id' => $this->region->id
        ];
        $updateDto = new SectorDTO($updateData);
        $updatedSector = $this->sectorService->update($sector->id, $updateDto);

        $this->assertEquals($updateData['name'], $updatedSector->name);
        $this->assertEquals($updateData['phone'], $updatedSector->phone);
    }

    /** @test */
    public function it_belongs_to_region()
    {
        $data = [
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $this->region->id
        ];
        $dto = new SectorDTO($data);
        $sector = $this->sectorService->create($dto);

        $this->assertInstanceOf(Region::class, $sector->region);
        $this->assertEquals($this->region->id, $sector->region->id);
    }

    /** @test */
    public function it_has_many_schools()
    {
        // Sektor yaradaq
        $data = [
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $this->region->id
        ];
        $dto = new SectorDTO($data);
        $sector = $this->sectorService->create($dto);

        // İki məktəb yaradaq
        School::create([
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $sector->id
        ]);

        School::create([
            'name' => '99 nömrəli məktəb',
            'utis_code' => '7654321',
            'phone' => '+994502345678',
            'email' => 'mekteb99@edu.gov.az',
            'sector_id' => $sector->id
        ]);

        $this->assertCount(2, $sector->schools);
        $this->assertInstanceOf(School::class, $sector->schools->first());
    }

    /** @test */
    public function it_cannot_delete_sector_with_schools()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Sektor yaradaq
        $data = [
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $this->region->id
        ];
        $dto = new SectorDTO($data);
        $sector = $this->sectorService->create($dto);

        // Məktəb yaradaq
        School::create([
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $sector->id
        ]);

        // Sektoru silməyə çalışaq
        $this->sectorService->delete($sector->id);
    }
}
