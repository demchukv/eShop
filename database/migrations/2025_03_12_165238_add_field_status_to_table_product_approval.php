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
        Schema::table('product_approvals', function (Blueprint $table) {
            $table->enum('status', ['approved', 'disapproved'])->default('approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_approvals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
