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
        Schema::create('mm_memorial_questions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->bigIncrements('id')->unique();
            $table->unsignedBigInteger('detail_id')->comment('질문 상세 정보 고유키');
            $table->unsignedInteger('display_order')->default(0)->comment('표시 순서');
            $table->unsignedTinyInteger('is_active')->default(1)->comment('활성화 여부(0:false, 1:true)');
            $table->timestamps();

            $table->foreign('detail_id')->references('id')->on('mm_memorial_question_details')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_memorial_questions');
    }
};
