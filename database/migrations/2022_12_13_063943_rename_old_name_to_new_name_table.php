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
        Schema::connection('backups')->rename('ozon_orders', 'ozon__fbs_rfbs_orders');
        Schema::connection('backups')->rename('ozon_orders_delivery_methods', 'ozon_fbs_rfbs_orders_delivery_methods');
        Schema::connection('backups')->rename('ozon_orders_cancellations', 'ozon_fbs_rfbs_orders_cancellations');
        Schema::connection('backups')->rename('ozon_orders_analitics_data', 'ozon_fbs_rfbs_orders_analitics_data');
        Schema::connection('backups')->rename('ozon_orders_products', 'ozon_fbs_rfbs_orders_products');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('backups')->rename('ozon__fbs_rfbs_orders', 'ozon_orders');
        Schema::connection('backups')->rename('ozon_fbs_rfbs_orders_delivery_methods', 'ozon_orders_delivery_methods');
        Schema::connection('backups')->rename('ozon_fbs_rfbs_orders_cancellations', 'ozon_orders_cancellations');
        Schema::connection('backups')->rename('ozon_fbs_rfbs_orders_analitics_data', 'ozon_orders_analitics_data');
        Schema::connection('backups')->rename('ozon_fbs_rfbs_orders_products', 'ozon_orders_products');
    }
};
