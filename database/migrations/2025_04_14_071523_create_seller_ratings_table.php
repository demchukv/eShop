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
        Schema::create('seller_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->integer('store_id');
            $table->integer('order_id');
            $table->integer('user_id');
            $table->text('comment');
            $table->unsignedTinyInteger('quality_of_service')->comment('Rating from 1 to 5');
            $table->unsignedTinyInteger('on_time_delivery')->comment('Rating from 1 to 5');
            $table->unsignedTinyInteger('relevance_price_availability')->comment('Rating from 1 to 5');
            $table->timestamps();

            // Індекси та зовнішні ключі
            $table->foreign('seller_id')->references('id')->on('seller_data')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Унікальний індекс, щоб уникнути дублювання відгуків
            $table->unique(['user_id', 'order_id', 'store_id'], 'unique_seller_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_ratings');
    }
};
