<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_approvals', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('manager_id');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->engine = "InnoDB";
        });

        Schema::table('product_approvals', function ($table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::table('product_approvals', function ($table) {
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('product_approvals', function ($table) {
            $table->unique(['product_id', 'manager_id']); // Кожен менеджер може підтвердити товар лише раз
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_approvals');
        Schema::enableForeignKeyConstraints();
    }
};
