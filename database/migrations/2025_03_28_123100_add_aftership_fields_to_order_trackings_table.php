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
            $table->string('aftership_tracking_id')->nullable()->after('tracking_number'); // Додаємо поле для ID трекінгу
            $table->text('aftership_data')->nullable()->after('aftership_tracking_id');     // Додаємо поле для повної відповіді API
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_trackings', function (Blueprint $table) {
            $table->dropColumn(['aftership_tracking_id', 'aftership_data']);
        });
    }
};
