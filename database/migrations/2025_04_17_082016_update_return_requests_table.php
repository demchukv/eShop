<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->integer('order_tracking_id')->nullable()->after('evidence_path');
            $table->foreign('order_tracking_id')->references('id')->on('order_trackings')->onDelete('set null');
            $table->dropColumn(['tracking_number', 'courier_service']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('evidence_path');
            $table->string('courier_service')->nullable()->after('tracking_number');
            $table->dropForeign(['order_tracking_id']);
            $table->dropColumn('order_tracking_id');
        });
    }
};
