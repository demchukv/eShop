<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Виконання міграції.
     */
    public function up(): void
    {
        Schema::create('seller_invites', function (Blueprint $table) {
            $table->id();
            $table->string('link', 25)->unique();
            $table->integer('user_id');
            $table->timestamps();

            $table->engine = "InnoDB";
        });
        Schema::table('seller_invites', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Скасування міграції.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_invites');
        Schema::enableForeignKeyConstraints();
    }
};
