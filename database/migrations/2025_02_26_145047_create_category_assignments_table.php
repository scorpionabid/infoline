<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('category_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->enum('assigned_type', ['sector', 'school', 'all']);
            $table->foreignId('assigned_id')->nullable();
            $table->timestamps();
            
            $table->unique(['category_id', 'assigned_type', 'assigned_id'],'cat_assign_unique');
        });
        
        Schema::create('column_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('column_id')->constrained('columns')->onDelete('cascade');
            $table->string('value');
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('column_choices');
        Schema::dropIfExists('category_assignments');
    }
}