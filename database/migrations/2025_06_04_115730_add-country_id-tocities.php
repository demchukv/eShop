<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedMediumInteger('country_id')->nullable()->after('id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
