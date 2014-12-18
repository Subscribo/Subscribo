<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\KochaboRecipe
 */
class CreateKochaboRecipesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kochabo_recipes', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('country_id')->unsigned();
            $table->string('source', 255)->nullable();
            $table->string('identifier', 255);
            $table->string('comment', 255)->nullable();
            $table->timestamps();
            $table->foreign('country_id', 'kochabo_recipes_country_id_foreign')->references('id')->on('countries');
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
        Schema::dropIfExists('kochabo_recipes');
    }
}
