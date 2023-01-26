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
        Schema::create('insales_bonus_system', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id');
            $table->string('in_order_id');
            $table->string('client_id');
            $table->string('bonus');
            $table->tinyInteger('exported')->nullable();
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
        Schema::dropIfExists('insales_bonus_system');
    }
};
