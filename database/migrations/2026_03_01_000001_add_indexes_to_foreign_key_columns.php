<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mm_memorials', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('mm_stories', function (Blueprint $table) {
            $table->index('memorial_id');
            $table->index('user_id');
        });

        Schema::table('mm_visitor_comments', function (Blueprint $table) {
            $table->index('memorial_id');
            $table->index('user_id');
        });

        Schema::table('mm_purchase_requests', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::table('mm_memorials', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('mm_stories', function (Blueprint $table) {
            $table->dropIndex(['memorial_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('mm_visitor_comments', function (Blueprint $table) {
            $table->dropIndex(['memorial_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('mm_purchase_requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
