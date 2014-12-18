<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Translation
 */
class CreateTranslationsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('table_id')->unsigned();
            $table->integer('field_id')->unsigned();
            $table->integer('row_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('text', 255);
            $table->timestamps();
            $table->foreign('table_id', 'translations_table_id_foreign')->references('id')->on('tables');
            $table->foreign('field_id', 'translations_field_id_foreign')->references('id')->on('table_fields');
            $table->foreign('language_id', 'translations_language_id_foreign')->references('id')->on('languages');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
