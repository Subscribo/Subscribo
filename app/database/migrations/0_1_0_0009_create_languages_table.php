<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Language
 */
class CreateLanguagesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('english_name', 255);
            $table->string('german_name', 255);
            $table->string('native_name', 255);
            $table->integer('fallback_language_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('fallback_language_id', 'languages_fallback_language_id_foreign')->references('id')->on('languages');
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
        Schema::dropIfExists('languages');
    }
}
