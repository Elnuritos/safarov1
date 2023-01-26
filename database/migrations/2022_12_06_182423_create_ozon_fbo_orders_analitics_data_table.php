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
        Schema::connection('backups')->create('ozon_fbo_orders_analitics_data', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id');
            $table->bigInteger('warehouse_id');
            $table->string('region');
            $table->string('city');
            $table->string('delivery_type')->nullable();
            $table->string('payment_type_group_name');
            $table->string('warehouse_name');

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
        Schema::connection('backups')->dropIfExists('ozon_fbo_orders_analitics_data');
    }
};
