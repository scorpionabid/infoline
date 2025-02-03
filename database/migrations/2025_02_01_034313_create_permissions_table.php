<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // İcazə adı
            $table->string('slug')->unique(); // Texniki ad (unique olmalıdır)
            $table->string('description')->nullable();
            $table->string('group')->nullable(); // İcazə qrupu
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};