<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm_attachments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->bigIncrements('id')->unique();
            $table->string('file_path', 255)->default('')->comment('파일 경로');
            $table->string('file_name', 255)->default('')->comment('파일명');
            $table->unsignedTinyInteger('is_delete')->default(0)->comment('삭제 여부(0:false, 1:true)');
            $table->timestamp('deleted_at')->nullable()->comment('삭제 시간');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_attachments');
    }
};
