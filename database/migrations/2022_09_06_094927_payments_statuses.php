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
        Schema::create('payments_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_id');
            $table->string('task_id');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('payments_statuses');
    }
};
