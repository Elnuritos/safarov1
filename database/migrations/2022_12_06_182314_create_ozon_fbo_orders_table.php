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
        Schema::connection('backups')->create('ozon_fbo_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id');
            $table->string('posting_number');
            $table->string('status');
            $table->string('order_number');
            $table->dateTimeTz('in_process_at');
            $table->dateTimeTz('created_at_ozon');

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
        Schema::connection('backups')->dropIfExists('ozon_fbo_orders');
    }
};
