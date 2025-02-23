<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['text', 'number', 'date', 'select', 'textarea'])->default('text');
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->date('end_date')->nullable();
            $table->integer('input_limit')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('columns');
    }
};
