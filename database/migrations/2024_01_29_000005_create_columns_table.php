<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('columns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('data_type', ['text', 'number', 'date', 'select', 'multiselect', 'file']);
            $table->date('end_date')->nullable();
            $table->integer('input_limit')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('columns');
    }
};
