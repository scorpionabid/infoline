<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('type')->after('utis_code');
            $table->foreignId('region_id')->after('sector_id')->nullable()->constrained();
            $table->text('address')->nullable()->after('email');
            $table->boolean('status')->default(true)->after('address');
            
            // Make some fields nullable
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['type', 'region_id', 'address', 'status']);
            $table->string('phone')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
        });
    }
};
