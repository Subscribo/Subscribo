<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Realization
 */
class CreateRealizationsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realizations', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('comment', 255)->nullable();
            $table->integer('product_id')->unsigned();
            $table->integer('delivery_id')->unsigned();
            $table->timestamps();
            $table->foreign('product_id', 'realizations_product_id_foreign')->references('id')->on('products');
            $table->foreign('delivery_id', 'realizations_delivery_id_foreign')->references('id')->on('deliveries');
            $table->unique('identifier');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('realizations');
    }
}
