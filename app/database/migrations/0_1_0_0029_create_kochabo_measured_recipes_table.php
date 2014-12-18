<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\KochaboMeasuredRecipe
 */
class CreateKochaboMeasuredRecipesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kochabo_measured_recipes', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('recipe_id')->unsigned();
            $table->tinyInteger('persons_count');
            $table->timestamps();
            $table->foreign('product_id', 'kochabo_measured_recipes_product_id_foreign')->references('id')->on('products');
            $table->foreign('recipe_id', 'kochabo_measured_recipes_recipe_id_foreign')->references('id')->on('kochabo_recipes');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kochabo_measured_recipes');
    }
}
