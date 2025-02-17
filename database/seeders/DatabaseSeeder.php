<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\User;
use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Domain\Enums\UserType;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Əvvəlcə Permission və Role-ları yaradaq
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // 2. Super Admin yaradırıq
        $superAdmin = User::factory()->superAdmin()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@example.com',
            'username' => 'superadmin',
            'utis_code' => '1000000',
            'password' => bcrypt('password'),
        ]);

        // Super Admin-ə super-admin rolunu təyin edirik
        $superAdmin->roles()->attach(
            \App\Domain\Entities\Role::where('slug', 'super-admin')->first()
        );

        // 3. Regionlar və digər strukturu yaradaq
        $regions = Region::factory(3)->create();

        foreach ($regions as $region) {
            // Hər region üçün 2 sektor
            $sectors = Sector::factory(2)->create([
                'region_id' => $region->id
            ]);

            // Hər sektor üçün bir SectorAdmin
            foreach ($sectors as $sector) {
                $sectorAdmin = User::factory()->sectorAdmin()->create([
                    'region_id' => $region->id,
                    'sector_id' => $sector->id,
                    'password' => bcrypt('password'),
                ]);

                // Sector Admin-ə sector-admin rolunu təyin edirik
                $sectorAdmin->roles()->attach(
                    \App\Domain\Entities\Role::where('slug', 'sector-admin')->first()
                );

                // Hər sektor üçün 3 məktəb
                $schools = School::factory(3)->create([
                    'sector_id' => $sector->id
                ]);

                // Hər məktəb üçün bir SchoolAdmin
                foreach ($schools as $school) {
                    $schoolAdmin = User::factory()->schoolAdmin()->create([
                        'region_id' => $region->id,
                        'sector_id' => $sector->id,
                        'school_id' => $school->id,
                        'password' => bcrypt('password'),
                    ]);

                    // School Admin-ə school-admin rolunu təyin edirik
                    $schoolAdmin->roles()->attach(
                        \App\Domain\Entities\Role::where('slug', 'school-admin')->first()
                    );
                }
            }
        }
    }
}