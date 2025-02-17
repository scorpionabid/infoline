<?php

namespace Database\seeders;

use App\Domain\Entities\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
   public function run(): void
   {
       // Admin idarəetmə icazələri
       $adminPermissions = [
           [
               'name' => 'Sektor admin idarəetmə',
               'slug' => 'manage-sector-admins',
               'description' => 'Sektor adminlərini yaratmaq, redaktə və silmək',
               'group' => 'admin-management'
           ],
           [
               'name' => 'Məktəb admin idarəetmə',
               'slug' => 'manage-school-admins',
               'description' => 'Məktəb adminlərini yaratmaq, redaktə və silmək',
               'group' => 'admin-management'
           ],
       ];

       // Kateqoriya və sütun icazələri
       $categoryPermissions = [
           [
               'name' => 'Kateqoriya idarəetmə',
               'slug' => 'manage-categories',
               'description' => 'Kateqoriyaları yaratmaq, redaktə və silmək',
               'group' => 'category-management'
           ],
           [
               'name' => 'Sütun idarəetmə',
               'slug' => 'manage-columns',
               'description' => 'Sütunları yaratmaq, redaktə və silmək',
               'group' => 'category-management'
           ],
       ];

       // Məktəb məlumatları icazələri
       $schoolPermissions = [
           [
               'name' => 'Məktəb məlumatları idarəetmə',
               'slug' => 'manage-school-data',
               'description' => 'Məktəb məlumatlarını yaratmaq, redaktə və silmək',
               'group' => 'school-management'
           ],
       ];

       // Bütün icazələri bir array-də birləşdirib yaradaq
       $allPermissions = array_merge($adminPermissions, $categoryPermissions, $schoolPermissions);
       
       foreach ($allPermissions as $permission) {
           Permission::firstOrCreate(
               ['slug' => $permission['slug']],
               $permission
           );  
       }
   }
}