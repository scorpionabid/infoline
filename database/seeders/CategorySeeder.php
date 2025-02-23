<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\Category;
use App\Domain\Entities\Column;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Məktəb məlumatları kateqoriyası
        $schoolInfo = Category::create([
            'name' => 'Məktəb məlumatları',
            'description' => 'Məktəbin əsas məlumatları',
            'is_active' => true,
            'order' => 1
        ]);

        // Sütunlar
        Column::create([
            'category_id' => $schoolInfo->id,
            'name' => 'Məktəbin adı',
            'description' => 'Məktəbin tam rəsmi adı',
            'type' => 'text',
            'required' => true,
            'order' => 1,
            'is_active' => true
        ]);

        Column::create([
            'category_id' => $schoolInfo->id,
            'name' => 'Şagird sayı',
            'description' => 'Ümumi şagird sayı',
            'type' => 'number',
            'required' => true,
            'validation_rules' => json_encode(['min' => 0]),
            'order' => 2,
            'is_active' => true
        ]);

        Column::create([
            'category_id' => $schoolInfo->id,
            'name' => 'Tədris dili',
            'type' => 'select',
            'required' => true,
            'options' => json_encode(['az' => 'Azərbaycan dili', 'ru' => 'Rus dili']),
            'order' => 3,
            'is_active' => true
        ]);

        // İnfrastruktur məlumatları
        $infrastructure = Category::create([
            'name' => 'İnfrastruktur',
            'description' => 'Məktəbin infrastruktur məlumatları',
            'is_active' => true,
            'order' => 2
        ]);

        Column::create([
            'category_id' => $infrastructure->id,
            'name' => 'Sinif otaqlarının sayı',
            'type' => 'number',
            'required' => true,
            'validation_rules' => json_encode(['min' => 0]),
            'order' => 1,
            'is_active' => true
        ]);

        Column::create([
            'category_id' => $infrastructure->id,
            'name' => 'İdman zalı',
            'type' => 'select',
            'required' => true,
            'options' => json_encode(['var' => 'Var', 'yox' => 'Yoxdur']),
            'order' => 2,
            'is_active' => true
        ]);
    }
}