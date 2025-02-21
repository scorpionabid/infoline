<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'user_type')) {
            // Temporary column yaradırıq
            if (!Schema::hasColumn('users', 'user_type_temp')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('user_type_temp')->after('user_type');
                });
            }

            // Mövcud dəyərləri temporary column-a köçürürük
            DB::table('users')->update([
                'user_type_temp' => DB::raw('CASE 
                    WHEN user_type = "sectoradmin" THEN "sector-admin"
                    WHEN user_type = "schooladmin" THEN "school-admin"
                    ELSE user_type 
                    END')
            ]);

            // Köhnə user_type sütununu silirik
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });

            // Yeni user_type sütununu yaradırıq
            Schema::table('users', function (Blueprint $table) {
                $table->enum('user_type', ['superadmin', 'sector-admin', 'school-admin'])->after('user_type_temp');
            });

            // Temporary column-dan yeni sütuna məlumatları köçürürük
            DB::table('users')->update([
                'user_type' => DB::raw('user_type_temp')
            ]);

            // Temporary column-u silirik
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type_temp');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'user_type')) {
            // Temporary column yaradırıq
            if (!Schema::hasColumn('users', 'user_type_temp')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('user_type_temp')->after('user_type');
                });
            }

            // Mövcud dəyərləri temporary column-a köçürürük
            DB::table('users')->update([
                'user_type_temp' => DB::raw('CASE 
                    WHEN user_type = "sector-admin" THEN "sectoradmin"
                    WHEN user_type = "school-admin" THEN "schooladmin"
                    ELSE user_type 
                    END')
            ]);

            // Köhnə user_type sütununu silirik
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });

            // Yeni user_type sütununu yaradırıq
            Schema::table('users', function (Blueprint $table) {
                $table->enum('user_type', ['superadmin', 'sectoradmin', 'schooladmin'])->after('user_type_temp');
            });

            // Temporary column-dan yeni sütuna məlumatları köçürürük
            DB::table('users')->update([
                'user_type' => DB::raw('user_type_temp')
            ]);

            // Temporary column-u silirik
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type_temp');
            });
        }
    }
};