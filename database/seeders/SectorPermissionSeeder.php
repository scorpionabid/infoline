<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        foreach (Permission::SECTOR_PERMISSIONS as $slug => $name) {
            Permission::create([
                'name' => $name,
                'slug' => $slug,
                'group' => 'sector',
                'description' => $name
            ]);
        }
    }
}
