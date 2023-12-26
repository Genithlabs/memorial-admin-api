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
        Schema::create('mm_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->bigIncrements('id');
            $table->string('email', 100)->unique()->default('')->comment('이메일');
            $table->string('user_name', 50)->default('')->comment('회원 이름');
            $table->string('user_password', 255)->default('')->comment('비밀번호');
            $table->tinyInteger('is_trial')->default(0)->comment('트라이얼 여부 0:false, 1:true');
            $table->timestamp('trial_time')->nullable()->comment('트라이얼 시작 시간');
            $table->timestamp('last_login_time')->nullable()->comment('마지막 로그인 시간');
            $table->tinyInteger('is_withdraw')->default(0)->comment('탈퇴 여부 0:false, 1:true');
            $table->timestamp('withdraw_time')->nullable()->comment('탈퇴 시간');
            $table->tinyInteger('is_dormancy')->default(0)->comment('휴면 여뷰 0:false, 1:true');
            $table->timestamp('dormancy_time')->nullable()->comment('휴면 시간');
            $table->rememberToken();
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
        Schema::dropIfExists('mm_users');
    }
};
