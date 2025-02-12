<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sectors', function (Blueprint $table) {
            // admin_id sütununu əlavə edirik
            $table->unsignedBigInteger('admin_id')->nullable();
            
            // Foreign key əlaqəsi
            $table->foreign('admin_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sectors', function (Blueprint $table) {
            // Geri qaytarma (rollback) üçün
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};