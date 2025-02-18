<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sector_admin_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sector_id')->constrained()->onDelete('cascade');
            $table->enum('admin_type', ['primary', 'secondary'])->default('primary');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['user_id', 'sector_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sector_admin_roles');
    }
};