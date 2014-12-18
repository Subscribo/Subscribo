<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Service
 */
class CreateServicesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('name', 255);
            $table->string('comment', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->integer('default_language_id')->unsigned();
            $table->integer('operator_id')->unsigned()->default(1);
            $table->timestamps();
            $table->foreign('default_language_id', 'services_default_language_id_foreign')->references('id')->on('languages');
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
        Schema::dropIfExists('services');
    }
}
