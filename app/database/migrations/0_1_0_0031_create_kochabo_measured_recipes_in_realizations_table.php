<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\KochaboMeasuredRecipesInRealization
 */
class CreateKochaboMeasuredRecipesInRealizationsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kochabo_measured_recipes_in_realizations', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('realization_id')->unsigned();
            $table->integer('measured_recipe_id')->unsigned();
            $table->timestamps();
            $table->foreign('realization_id', 'kochabo_measured_recipes_in_realizations_realization_id_foreign')->references('id')->on('realizations');
            $table->foreign('measured_recipe_id', 'kochabo_measured_recipes_in_realizations_measured_reci_foreign_2')->references('id')->on('kochabo_measured_recipes');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kochabo_measured_recipes_in_realizations');
    }
}
