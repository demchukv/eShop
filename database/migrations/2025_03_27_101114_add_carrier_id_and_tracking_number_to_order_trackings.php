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
        Schema::table('order_trackings', function (Blueprint $table) {
            $table->string('carrier_id')->nullable(); // ID перевізника
            $table->string('tracking_number')->nullable();        // Трек-код
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_trackings', function (Blueprint $table) {
            $table->dropColumn('carrier_id');
            $table->dropColumn('tracking_number');
        });
    }
};
