<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\KochaboRecipeStep
 */
class CreateKochaboRecipeStepsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kochabo_recipe_steps', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('sequence');
            $table->integer('recipe_id')->unsigned();
            $table->string('identifier', 255);
            $table->timestamps();
            $table->foreign('recipe_id', 'kochabo_recipe_steps_recipe_id_foreign')->references('id')->on('kochabo_recipes');
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
        Schema::dropIfExists('kochabo_recipe_steps');
    }
}
