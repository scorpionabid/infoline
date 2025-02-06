<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('method');
            $table->string('url');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->json('request_data')->nullable();
            $table->integer('response_code')->nullable();
            $table->float('duration_ms')->nullable();
            $table->string('memory_usage')->nullable();
            $table->timestamps();

            // 20 dəqiqədən köhnə logları silmək üçün index
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}