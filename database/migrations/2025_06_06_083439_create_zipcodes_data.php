<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zipcodes_data', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->json('data')->nullable(); // JSON для зберігання properties
            $table->timestamps();

            // Зовнішній ключ до zipcodes.id з каскадним видаленням
            $table->foreign('id')
                ->references('id')
                ->on('zipcodes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zipcodes_data');
    }
};
