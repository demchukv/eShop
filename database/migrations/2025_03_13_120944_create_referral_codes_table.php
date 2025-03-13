<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralCodesTable extends Migration
{
    public function up()
    {
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Короткий унікальний код (наприклад, 8-10 символів)
            $table->integer('product_id');
            $table->integer('dealer_id');
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::table('referral_codes', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::table('referral_codes', function (Blueprint $table) {
            $table->foreign('dealer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('referral_codes');
    }
}
