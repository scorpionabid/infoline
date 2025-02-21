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
            'type' => 'standard'
        ]);

        // Sütunlar
        Column::create([
            'category_id' => $schoolInfo->id,
            'name' => 'Məktəbin adı',
            'description' => 'Məktəbin tam rəsmi adı',
            'type' => 'text',
            'required' => true,
            'order' => 1
        ]);

        Column::create([
            'category_id' => $schoolInfo->id,
            'name' => 'Şagird sayı',
            'description' => 'Ümumi şagird sayı',
            'type' => 'number',
            'required' => true,
            'validation_rules' => ['min' => 0],
            'order' => 2
        ]);

        Column::create([
            'category_id' => $schoolInfo->id,
            'name' => 'Tədris dili',
            'type' => 'select',
            'required' => true,
            'options' => ['az' => 'Azərbaycan dili', 'ru' => 'Rus dili'],
            'order' => 3
        ]);

        // Müəllim məlumatları kateqoriyası
        $teacherInfo = Category::create([
            'name' => 'Müəllim məlumatları',
            'description' => 'Müəllim heyəti haqqında məlumatlar',
            'type' => 'dynamic'
        ]);

        // Sütunlar
        Column::create([
            'category_id' => $teacherInfo->id,
            'name' => 'Müəllimin adı',
            'description' => 'Müəllimin adı və soyadı',
            'type' => 'text',
            'required' => true,
            'order' => 1
        ]);

        Column::create([
            'category_id' => $teacherInfo->id,
            'name' => 'İxtisas',
            'type' => 'text',
            'required' => true,
            'order' => 2
        ]);

        Column::create([
            'category_id' => $teacherInfo->id,
            'name' => 'İş təcrübəsi (il)',
            'type' => 'number',
            'required' => true,
            'validation_rules' => ['min' => 0],
            'order' => 3
        ]);

        // Hesabat kateqoriyası
        $report = Category::create([
            'name' => 'Rüblük hesabat',
            'description' => 'Rüblük statistik hesabat',
            'type' => 'report'
        ]);

        // Sütunlar
        Column::create([
            'category_id' => $report->id,
            'name' => 'Hesabat dövrü',
            'type' => 'select',
            'required' => true,
            'options' => [
                'q1' => '1-ci rüb',
                'q2' => '2-ci rüb',
                'q3' => '3-cü rüb',
                'q4' => '4-cü rüb'
            ],
            'order' => 1
        ]);

        Column::create([
            'category_id' => $report->id,
            'name' => 'Olimpiada iştirakçıları',
            'type' => 'number',
            'required' => true,
            'validation_rules' => ['min' => 0],
            'order' => 2
        ]);

        Column::create([
            'category_id' => $report->id,
            'name' => 'Qeydlər',
            'type' => 'textarea',
            'required' => false,
            'order' => 3
        ]);
    }
}