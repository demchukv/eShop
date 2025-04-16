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
        // Видаляємо старий зовнішній ключ
        Schema::table('disputs', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
        });

        // Оновлюємо seller_id, підставляючи user_id із seller_data
        DB::table('disputs')
            ->join('seller_data', 'disputs.seller_id', '=', 'seller_data.id')
            ->update(['disputs.seller_id' => DB::raw('seller_data.user_id')]);

        // Додаємо новий зовнішній ключ до users.id
        Schema::table('disputs', function (Blueprint $table) {
            $table->integer('seller_id')->change();
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Видаляємо новий зовнішній ключ
        Schema::table('disputs', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
        });

        // Повертаємо seller_id до seller_data.id (зворотне оновлення)
        DB::table('disputs')
            ->join('seller_data', 'disputs.seller_id', '=', 'seller_data.user_id')
            ->update(['disputs.seller_id' => DB::raw('seller_data.id')]);

        // Відновлюємо старий зовнішній ключ
        Schema::table('disputs', function (Blueprint $table) {
            $table->foreign('seller_id')->references('id')->on('seller_data')->onDelete('cascade');
        });
    }
};
