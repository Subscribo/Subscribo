<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\State
 */
class CreateStatesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('name', 255);
            $table->integer('country_id')->unsigned();
            $table->timestamps();
            $table->foreign('country_id', 'states_country_id_foreign')->references('id')->on('countries');
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
        Schema::dropIfExists('states');
    }
}
