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
            $table->decimal('refund_amount', 10, 2)->default(0)->after('is_refund');
            $table->string('refund_status')->nullable()->after('refund_amount');
            $table->string('refund_id')->nullable()->after('refund_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_status', 'refund_id']);
        });
    }
};
