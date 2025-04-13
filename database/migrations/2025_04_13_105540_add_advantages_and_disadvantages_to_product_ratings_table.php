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
        Schema::table('product_ratings', function (Blueprint $table) {
            $table->text('advantages')->nullable()->after('comment');
            $table->text('disadvantages')->nullable()->after('advantages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_ratings', function (Blueprint $table) {
            $table->dropColumn(['advantages', 'disadvantages']);
        });
    }
};
