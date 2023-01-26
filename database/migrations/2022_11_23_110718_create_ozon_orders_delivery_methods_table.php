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
        Schema::connection('backups')->create('ozon_orders_delivery_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id');
            $table->bigInteger('delivery_method_id');
            $table->bigInteger('warehouse_id');
            $table->integer('tpl_provider_id');
            $table->string('name');
            $table->string('warehouse');
            $table->string('tpl_provider');
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
        Schema::connection('backups')->dropIfExists('ozon_orders_delivery_methods');
    }
};
