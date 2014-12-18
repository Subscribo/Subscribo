<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\CountryUnion
 */
class CreateCountryUnionsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_unions', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('name', 255);
            $table->timestamps();
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
        Schema::dropIfExists('country_unions');
    }
}
