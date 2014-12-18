<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Delivery
 */
class CreateDeliveriesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->date('start');
            $table->integer('service_id')->unsigned();
            $table->timestamps();
            $table->foreign('service_id', 'deliveries_service_id_foreign')->references('id')->on('services');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
