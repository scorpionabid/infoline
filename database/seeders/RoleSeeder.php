<?php

namespace Database\seeders;

use App\Domain\Entities\Role;
use App\Domain\Entities\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
   public function run(): void
   {
       // SuperAdmin rolunu yaradaq
       $superAdmin = Role::create([
           'name' => 'Super Admin',
           'slug' => 'super-admin',
           'description' => 'Tam səlahiyyətli admin',
           'is_system' => true
       ]);

       // Sector Admin rolunu yaradaq
       $sectorAdmin = Role::create([
           'name' => 'Sector Admin',
           'slug' => 'sector-admin',
           'description' => 'Sektor üzrə məsul admin',
           'is_system' => true
       ]);

       // School Admin rolunu yaradaq
       $schoolAdmin = Role::create([
           'name' => 'School Admin',
           'slug' => 'school-admin',
           'description' => 'Məktəb üzrə məsul admin',
           'is_system' => true
       ]);

       // SuperAdmin-ə bütün icazələri verək
       $superAdmin->permissions()->attach(Permission::all());

       // SectorAdmin-ə aid icazələri verək
       $sectorAdmin->permissions()->attach(
           Permission::whereIn('slug', [
               'manage-school-admins',
               'manage-school-data'
           ])->get()
       );

       // SchoolAdmin-ə aid icazələri verək
       $schoolAdmin->permissions()->attach(
           Permission::where('slug', 'manage-school-data')->get()
       );
   }
}