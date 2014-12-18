<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\DeliveryWindow
 */
class CreateDeliveryWindowsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_windows', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->integer('delivery_window_type_id')->unsigned();
            $table->integer('delivery_id')->unsigned();
            $table->timestamps();
            $table->foreign('delivery_window_type_id', 'delivery_windows_delivery_window_type_id_foreign')->references('id')->on('delivery_window_types');
            $table->foreign('delivery_id', 'delivery_windows_delivery_id_foreign')->references('id')->on('deliveries');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_windows');
    }
}
