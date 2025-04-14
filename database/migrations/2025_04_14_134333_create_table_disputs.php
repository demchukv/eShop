<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Створюємо таблицю без foreign key constraints
        Schema::create('disputs', function (Blueprint $table) {
            $table->id();
            $table->integer('return_request_id');
            $table->integer('user_id');
            $table->integer('seller_id');
            $table->string('status')->default('open');
            $table->timestamps();
        });

        // Додаємо foreign key constraints
        Schema::table('disputs', function (Blueprint $table) {
            $table->foreign('return_request_id')
                ->references('id')
                ->on('return_requests')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('seller_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('disputs');
    }
};
