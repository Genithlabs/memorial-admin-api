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
        Schema::create('mm_visitor_comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->bigIncrements('id')->unique();
            $table->unsignedBigInteger('user_id')->default(0)->comment('회원 고유키');
            $table->unsignedBigInteger('memorial_id')->default(0)->comment('기념관 고유키');
            $table->text('message')->nullable()->comment('메세지');
            $table->unsignedTinyInteger('is_visible')->default(1)->comment('노출 여부(0:false, 1:true)');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('mm_users')->cascadeOnDelete();
            $table->foreign('memorial_id')->references('id')->on('mm_memorials')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_visitor_comments');
    }
};
