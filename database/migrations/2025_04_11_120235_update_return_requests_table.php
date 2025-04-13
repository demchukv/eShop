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
            $table->string('delivery_status')->nullable(); // "received" або "not_received"
            $table->string('reason')->nullable(); // Причина повернення
            $table->string('application_type')->nullable(); // "return_and_refund" або "refund_only"
            $table->decimal('refund_amount', 10, 2)->nullable(); // Сума повернення
            $table->string('refund_method')->nullable(); // "wallet" або "original_payment"
            $table->text('description')->nullable(); // Опис проблеми
            $table->string('evidence_path')->nullable(); // Шлях до файлу
            $table->string('return_method')->nullable(); // Метод повернення для "return_and_refund"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_status',
                'reason',
                'application_type',
                'refund_amount',
                'refund_method',
                'description',
                'evidence_path',
                'return_method',
            ]);
        });
    }
};
