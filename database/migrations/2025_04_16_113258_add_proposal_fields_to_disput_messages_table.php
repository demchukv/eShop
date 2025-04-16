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
        Schema::table('disput_messages', function (Blueprint $table) {
            $table->decimal('refund_amount', 10, 2)->nullable()->after('message');
            $table->string('application_type')->nullable()->after('refund_amount');
            $table->string('refund_method')->nullable()->after('application_type');
            $table->json('evidence_path')->nullable()->after('refund_method');
            $table->string('proposal_status')->nullable()->after('evidence_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disput_messages', function (Blueprint $table) {
            $table->dropColumn([
                'refund_amount',
                'application_type',
                'refund_method',
                'evidence_path',
                'proposal_status',
            ]);
        });
    }
};
