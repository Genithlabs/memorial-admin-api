<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mm_purchase_requests', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('user_id');
            $table->text('admin_memo')->nullable()->after('status');
            $table->timestamp('processed_at')->nullable()->after('admin_memo');
            $table->unsignedBigInteger('processed_by')->nullable()->after('processed_at');
        });
    }

    public function down()
    {
        Schema::table('mm_purchase_requests', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_memo', 'processed_at', 'processed_by']);
        });
    }
};
