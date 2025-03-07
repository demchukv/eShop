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
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->string('passport')->nullable()->after('status');
            $table->string('tax_id')->nullable()->after('passport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->dropColumn(['passport', 'tax_id']);
        });
    }
};
