<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\ApiMethod
 */
class CreateApiMethodsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_methods', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('name', 255);
            $table->string('comment', 255)->nullable();
            $table->integer('api_id')->unsigned();
            $table->boolean('element')->default(false);
            $table->enum('http_verb', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'));
            $table->timestamps();
            $table->foreign('api_id', 'api_methods_api_id_foreign')->references('id')->on('apis');
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
        Schema::dropIfExists('api_methods');
    }
}
