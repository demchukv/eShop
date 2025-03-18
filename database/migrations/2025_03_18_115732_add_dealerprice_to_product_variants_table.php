<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDealerpriceToProductVariantsTable extends Migration
{
    public function up()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Додаємо поле dealerprice після special_price
            $table->double('dealer_price')->after('special_price')->nullable(false);
        });
    }

    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Видаляємо поле dealerprice при відкаті
            $table->dropColumn('dealer_price');
        });
    }
}
