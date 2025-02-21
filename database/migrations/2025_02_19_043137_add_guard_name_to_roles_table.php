<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Yeni migration faylında
    // database/migrations/2025_02_19_083100_add_guard_name_to_roles_table.php
    
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'slug')) {
                $table->string('slug')->default('default-slug')->after('name');
            }
            $table->string('guard_name')->default('web')->after('slug');
        });
    }
    
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('guard_name');
        });
    }
};
