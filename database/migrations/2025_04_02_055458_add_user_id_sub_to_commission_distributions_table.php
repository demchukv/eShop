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
        Schema::table('commission_distributions', function (Blueprint $table) {
            $table->enum('user_id_sub', ['shareholders', 'company_one', 'company_two'])
                ->after('user_id')
                ->nullable(); // або ->default('shareholders'), якщо потрібне значення за замовчуванням
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_distributions', function (Blueprint $table) {
            $table->dropColumn('user_id_sub');
        });
    }
};
