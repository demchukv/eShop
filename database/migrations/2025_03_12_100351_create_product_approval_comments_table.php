<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductApprovalCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('product_approval_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('manager_id');
            $table->text('comment');
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::table('product_approval_comments', function ($table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::table('product_approval_comments', function ($table) {
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_approval_comments');
    }
}
