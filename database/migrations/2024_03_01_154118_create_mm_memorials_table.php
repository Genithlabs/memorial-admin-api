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
        Schema::create('mm_memorials', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->bigIncrements('id')->unique();
            $table->string('user_id', 50)->unique()->comment('유저아이디')->unique();
            $table->string('name', 50)->comment('기념인 이름');
            $table->date('birth_start')->nullable()->comment('태어난 일자');
            $table->date('birth_end')->nullable()->comment('마감한 일자');
            $table->text('career_contents')->nullable()->comment('생애');
            $table->unsignedInteger('profile_attachment_id')->nullable()->comment('프로필 사진 첨부 파일 고유키');
            $table->unsignedInteger('bgm_attachment_id')->nullable()->comment('배경음악 첨부 파일 고유키');
            $table->unsignedTinyInteger('is_open')->default(0)->comment('오픈 여부(0:false, 1:true)');
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
        Schema::dropIfExists('mm_memorials');
    }
};
