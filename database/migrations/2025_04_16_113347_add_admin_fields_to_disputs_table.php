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
        Schema::table('disputs', function (Blueprint $table) {
            $table->timestamp('admin_requested_at')->nullable()->after('status');
            $table->unsignedBigInteger('admin_requester_id')->nullable()->after('admin_requested_at');
            $table->string('status')->default('open')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disputs', function (Blueprint $table) {
            $table->dropColumn(['admin_requested_at', 'admin_requester_id']);
            $table->string('status')->default('open')->change();
        });
    }
};
