<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing user types to match enum values
        DB::table('users')->where('user_type', 'super')->update(['user_type' => 'super_admin']);
        DB::table('users')->where('user_type', 'sector')->update(['user_type' => 'sector_admin']);
        DB::table('users')->where('user_type', 'school')->update(['user_type' => 'school_admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert user types to old values
        DB::table('users')->where('user_type', 'super_admin')->update(['user_type' => 'super']);
        DB::table('users')->where('user_type', 'sector_admin')->update(['user_type' => 'sector']);
        DB::table('users')->where('user_type', 'school_admin')->update(['user_type' => 'school']);
    }
};
