<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\KochaboIngredientsInMeasuredRecipe
 */
class CreateKochaboIngredientsInMeasuredRecipesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kochabo_ingredients_in_measured_recipes', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('amount', 255);
            $table->integer('measured_recipe_id')->unsigned();
            $table->integer('ingredient_id')->unsigned();
            $table->integer('measure_id')->unsigned();
            $table->timestamps();
            $table->foreign('measured_recipe_id', 'kochabo_ingredients_in_measured_recipes_measured_recip_foreign_1')->references('id')->on('kochabo_measured_recipes');
            $table->foreign('ingredient_id', 'kochabo_ingredients_in_measured_recipes_ingredient_id_foreign')->references('id')->on('kochabo_ingredients');
            $table->foreign('measure_id', 'kochabo_ingredients_in_measured_recipes_measure_id_foreign')->references('id')->on('kochabo_measures');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kochabo_ingredients_in_measured_recipes');
    }
}
