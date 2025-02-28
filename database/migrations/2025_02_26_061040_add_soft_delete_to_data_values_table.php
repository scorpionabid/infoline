<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteToDataValuesTable extends Migration
{
    public function up()
    {
        Schema::table('data_values', function (Blueprint $table) {
            $table->softDeletes(); // adds deleted_at column
        });
    }

    public function down()
    {
        Schema::table('data_values', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}