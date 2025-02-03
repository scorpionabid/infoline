<?php

namespace Tests\Unit;

use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use App\Application\DTOs\SchoolDTO;
use App\Application\Services\SchoolService;
use App\Infrastructure\Repositories\SchoolRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    private SchoolService $schoolService;
    private SchoolRepository $schoolRepository;
    private Sector $sector;
    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schoolRepository = new SchoolRepository();
        $this->schoolService = new SchoolService($this->schoolRepository);

        // Test üçün region və sektor yaradaq
        $this->region = Region::create([
            'name' => 'Bakı şəhəri',
            'phone' => '+994501234567'
        ]);

        $this->sector = Sector::create([
            'name' => 'Binəqədi rayonu',
            'phone' => '+994501234567',
            'region_id' => $this->region->id
        ]);
    }

    /** @test */
    public function it_can_create_school()
    {
        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];

        $dto = new SchoolDTO($data);
        $school = $this->schoolService->create($dto);

        $this->assertInstanceOf(School::class, $school);
        $this->assertEquals($data['name'], $school->name);
        $this->assertEquals($data['utis_code'], $school->utis_code);
        $this->assertEquals($data['phone'], $school->phone);
        $this->assertEquals($data['email'], $school->email);
        $this->assertEquals($data['sector_id'], $school->sector_id);
    }

    /** @test */
    public function it_validates_school_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '', // boş ad
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];

        $dto = new SchoolDTO($data);
        $this->schoolService->create($dto);
    }

    /** @test */
    public function it_validates_utis_code_format()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '123', // yanlış format (7 rəqəm olmalıdır)
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];

        $dto = new SchoolDTO($data);
        $this->schoolService->create($dto);
    }

    /** @test */
    public function it_validates_unique_utis_code()
    {
        $this->expectException(\InvalidArgumentException::class);

        // İlk məktəbi yaradaq
        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];
        $dto = new SchoolDTO($data);
        $this->schoolService->create($dto);

        // Eyni UTIS kodu ilə başqa məktəb yaratmağa çalışaq
        $data['name'] = '99 nömrəli məktəb';
        $dto = new SchoolDTO($data);
        $this->schoolService->create($dto);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'invalid-email', // yanlış format
            'sector_id' => $this->sector->id
        ];

        $dto = new SchoolDTO($data);
        $this->schoolService->create($dto);
    }

    /** @test */
    public function it_validates_phone_format()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => 'invalid-phone', // yanlış format
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];

        $dto = new SchoolDTO($data);
        $this->schoolService->create($dto);
    }

    /** @test */
    public function it_belongs_to_sector()
    {
        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];
        $dto = new SchoolDTO($data);
        $school = $this->schoolService->create($dto);

        $this->assertInstanceOf(Sector::class, $school->sector);
        $this->assertEquals($this->sector->id, $school->sector->id);
    }

    /** @test */
    public function it_can_get_region_through_sector()
    {
        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];
        $dto = new SchoolDTO($data);
        $school = $this->schoolService->create($dto);

        $this->assertInstanceOf(Region::class, $school->region);
        $this->assertEquals($this->sector->region->id, $school->region->id);
    }

    /** @test */
    public function it_has_many_admins()
    {
        // Məktəb yaradaq
        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];
        $dto = new SchoolDTO($data);
        $school = $this->schoolService->create($dto);

        // İki məktəb administratoru yaradaq
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'One',
            'utis_code' => '1000001',
            'email' => 'admin1@edu.gov.az',
            'username' => 'admin1',
            'password' => 'Password123',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => $this->region->id,
            'sector_id' => $this->sector->id,
            'school_id' => $school->id
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Two',
            'utis_code' => '1000002',
            'email' => 'admin2@edu.gov.az',
            'username' => 'admin2',
            'password' => 'Password123',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => $this->region->id,
            'sector_id' => $this->sector->id,
            'school_id' => $school->id
        ]);

        $this->assertCount(2, $school->admins);
        $this->assertInstanceOf(User::class, $school->admins->first());
    }

    /** @test */
    public function it_cannot_delete_school_with_admins()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Məktəb yaradaq
        $data = [
            'name' => '20 nömrəli məktəb',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'mekteb20@edu.gov.az',
            'sector_id' => $this->sector->id
        ];
        $dto = new SchoolDTO($data);
        $school = $this->schoolService->create($dto);

        // Administrator yaradaq
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'One',
            'utis_code' => '1000003',
            'email' => 'admin1@edu.gov.az',
            'username' => 'admin1',
            'password' => 'Password123',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => $this->region->id,
            'sector_id' => $this->sector->id,
            'school_id' => $school->id
        ]);

        // Məktəbi silməyə çalışaq
        $this->schoolService->delete($school->id);
    }
}
