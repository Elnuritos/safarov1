<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('backups')->create('ozon_fbo_orders_products', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id');
            $table->string('name');
            $table->integer('quantity');
            $table->integer('price');
            $table->bigInteger('ProductTotal');
            $table->string('article');
            $table->string('offer_id');
            $table->bigInteger('sku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('backups')->dropIfExists('ozon_fbo_orders_products');
    }
};
