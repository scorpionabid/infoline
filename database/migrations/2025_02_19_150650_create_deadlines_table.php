<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Əvvəlcə deadlines cədvəli varsa onu silib yeni strukturu istifadə edəcəyik
        if (Schema::hasTable('deadlines')) {
            Schema::dropIfExists('deadlines');
        }
    }

    public function down()
    {
        // Geri qayıtma əməliyyatı yoxdur
    }
};