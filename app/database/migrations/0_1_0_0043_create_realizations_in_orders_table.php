<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\RealizationsInOrder
 */
class CreateRealizationsInOrdersTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realizations_in_orders', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->integer('realization_id')->unsigned();
            $table->integer('amount')->unsigned();
            $table->timestamps();
            $table->foreign('order_id', 'realizations_in_orders_order_id_foreign')->references('id')->on('orders');
            $table->foreign('realization_id', 'realizations_in_orders_realization_id_foreign')->references('id')->on('realizations');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('realizations_in_orders');
    }
}
