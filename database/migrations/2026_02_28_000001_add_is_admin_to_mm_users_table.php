<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mm_users', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_admin')->default(0)->after('user_password');
        });
    }

    public function down()
    {
        Schema::table('mm_users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
