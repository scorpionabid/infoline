<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('utis_code')->unique();
                $table->string('email')->unique();
                $table->string('username')->unique();
                $table->string('password');
                $table->enum('user_type', ['superadmin', 'sector-admin', 'school-admin']);
                $table->foreignId('region_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('sector_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
                
                // New login tracking columns
                $table->timestamp('last_login_at')->nullable();
                $table->string('last_login_ip')->nullable();
                
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};