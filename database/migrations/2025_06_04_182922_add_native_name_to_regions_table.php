<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->string('native_name')->nullable()->after('name');
            $table->unique(['name', 'country_id']);
        });
    }

    public function down()
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('native_name');
        });
    }
};
