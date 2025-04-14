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
        Schema::create('disput_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('disput_id')->unsigned();
            $table->integer('sender_id');
            $table->text('message');
            $table->timestamps();
        });

        // Додаємо foreign key constraints
        Schema::table('disput_messages', function (Blueprint $table) {
            $table->foreign('disput_id')
                ->references('id')
                ->on('disputs')
                ->onDelete('cascade');
            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('disput_messages');
    }
};
