<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates pivot table for simple many to many relation
 * Related tables: languages, services
 */
class CreateLanguageServiceTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_service', function(Blueprint $table)
        {
            $table->bigIncrements('id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->integer('service_id')->unsigned();
            $table->bigInteger('ordering')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('language_id', 'language_service_language_id_foreign')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('service_id', 'language_service_service_id_foreign')->references('id')->on('services')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language_service');
    }
}
