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
        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->enum('type', ['dealer', 'manager']);
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->mediumText('photos')->nullable();
            $table->mediumText('message')->nullable();
            $table->mediumText('notes')->nullable();
            $table->timestamps();

            $table->engine = "InnoDB";
        });
        Schema::table('user_statuses', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_statuses');
        Schema::enableForeignKeyConstraints();
    }
};
