// database/migrations/2025_02_19_153200_create_school_data_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Əvvəlcə school_data cədvəli varsa onu silib yeni strukturu (data_values) istifadə edəcəyik
        if (Schema::hasTable('school_data')) {
            Schema::dropIfExists('school_data');
        }
    }

    public function down()
    {
        // Geri qayıtma əməliyyatı yoxdur
    }
};