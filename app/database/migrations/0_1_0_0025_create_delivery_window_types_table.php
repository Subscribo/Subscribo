<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\DeliveryWindowType
 */
class CreateDeliveryWindowTypesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_window_types', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->tinyInteger('day_of_week');
            $table->time('start');
            $table->integer('duration');
            $table->integer('service_id')->unsigned();
            $table->timestamps();
            $table->foreign('service_id', 'delivery_window_types_service_id_foreign')->references('id')->on('services');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_window_types');
    }
}
