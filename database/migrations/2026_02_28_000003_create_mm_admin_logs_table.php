<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mm_admin_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('admin_id')->comment('관리자 고유키');
            $table->string('action', 100)->comment('행위');
            $table->string('target_type', 50)->comment('대상 종류');
            $table->unsignedBigInteger('target_id')->comment('대상 ID');
            $table->string('description', 500)->default('')->comment('상세 설명');
            $table->timestamp('created_at')->nullable();

            $table->foreign('admin_id')->references('id')->on('mm_users')->cascadeOnDelete();
            $table->index(['target_type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mm_admin_logs');
    }
};
