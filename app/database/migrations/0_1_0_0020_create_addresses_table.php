<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Address
 */
class CreateAddressesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->tinyInteger('type')->default(1);
            $table->string('first_line', 255)->nullable();
            $table->string('second_line', 255)->nullable();
            $table->string('street', 255)->nullable();
            $table->string('house', 255)->nullable();
            $table->string('stairway', 255)->nullable();
            $table->string('floor', 255)->nullable();
            $table->string('apartment', 255)->nullable();
            $table->string('post_code', 255)->nullable();
            $table->string('city', 255);
            $table->string('district', 255)->nullable();
            $table->string('province', 255)->nullable();
            $table->integer('state_id')->unsigned()->nullable();
            $table->integer('country_id')->unsigned();
            $table->enum('country_union', array('EU'))->nullable();
            $table->string('gps_longitude', 255)->nullable();
            $table->string('gps_latitude', 255)->nullable();
            $table->bigInteger('contact_phone')->unsigned()->nullable();
            $table->text('delivery_information');
            $table->timestamps();
            $table->foreign('state_id', 'addresses_state_id_foreign')->references('id')->on('states');
            $table->foreign('country_id', 'addresses_country_id_foreign')->references('id')->on('countries');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
