<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionDistributionsTable extends Migration
{
    public function up()
    {
        Schema::create('commission_distributions', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->index();
            $table->integer('user_id')->index();
            $table->double('amount', 15, 2);
            $table->text('message')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });
        // Індекси вже додані через ->index()
        Schema::table('commission_distributions', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
        Schema::table('commission_distributions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commission_distributions');
    }
}
