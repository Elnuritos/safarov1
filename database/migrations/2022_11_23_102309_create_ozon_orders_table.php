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
        Schema::connection('backups')->create('ozon_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('posting_number');
            $table->string('status');
            $table->bigInteger('order_id');
            $table->string('order_number');
            $table->string('tracking_number')->nullable();
            $table->string('tpl_integration_type');
            $table->dateTimeTz('in_process_at');
            $table->dateTimeTz('shipment_date');
            $table->dateTime('delivering_date')->nullable();
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
        Schema::connection('backups')->dropIfExists('ozon_orders');
    }
};
