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
        Schema::connection('backups')->create('ozon_orders_cancellations', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id');
            $table->integer('cancel_reason_id')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->string('cancellation_type')->nullable();
            $table->string('cancellation_initiator')->nullable();
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
        Schema::connection('backups')->dropIfExists('ozon_orders_cancellations');
    }
};
