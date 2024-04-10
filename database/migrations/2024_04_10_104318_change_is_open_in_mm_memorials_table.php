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
        Schema::table('mm_memorials', function (Blueprint $table) {
            $table->string('is_open')->default(1)->comment('오픈 여부(0:false, 1:true)')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm_memorials', function (Blueprint $table) {
            $table->string('is_open')->default(0)->comment('오픈 여부(0:false, 1:true)')->change();
        });
    }
};
