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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_source')->nullable()->after('is_refund');
            $table->unsignedBigInteger('original_transaction_id')->nullable()->after('refund_id');

            // Додаємо індекси для оптимізації запитів
            $table->index('payment_source');
            $table->index('original_transaction_id');
        });

        // Заповнення нових полів для існуючих записів
        DB::statement("UPDATE transactions SET payment_source =
        CASE
            WHEN type = 'wallet' THEN 'wallet'
            WHEN type = 'stripe' THEN 'stripe'
            WHEN type = 'razorpay' THEN 'razorpay'
            WHEN type = 'paypal' THEN 'paypal'
            WHEN type = 'paystack' THEN 'paystack'
            WHEN type = 'phonepe' THEN 'phonepe'
            ELSE 'other'
        END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_source', 'original_transaction_id']);
        });
    }
};
