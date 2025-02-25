<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, convert existing values
        DB::table('users')->where('user_type', 'superadmin')->update(['user_type' => 'super']);
        DB::table('users')->where('user_type', 'sectoradmin')->update(['user_type' => 'sector']);
        DB::table('users')->where('user_type', 'schooladmin')->update(['user_type' => 'school']);

        // Then modify the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('super', 'sector', 'school')");
    }

    public function down()
    {
        // Convert back to old values
        DB::table('users')->where('user_type', 'super')->update(['user_type' => 'superadmin']);
        DB::table('users')->where('user_type', 'sector')->update(['user_type' => 'sectoradmin']);
        DB::table('users')->where('user_type', 'school')->update(['user_type' => 'schooladmin']);

        // Then modify the enum back
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('superadmin', 'sectoradmin', 'schooladmin')");
    }
};
