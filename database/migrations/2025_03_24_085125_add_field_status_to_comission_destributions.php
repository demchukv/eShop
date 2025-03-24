<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('commission_distributions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'canceled'])
                ->default('pending')
                ->after('message');
        });
    }

    public function down()
    {
        Schema::table('commission_distributions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
