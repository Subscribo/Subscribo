<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Picture
 */
class CreatePicturesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pictures', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('comment', 255)->nullable();
            $table->tinyInteger('file_format')->nullable();
            $table->boolean('transparent_background')->default(false);
            $table->tinyInteger('size_format')->nullable();
            $table->integer('original_height')->unsigned()->nullable();
            $table->integer('original_width')->unsigned()->nullable();
            $table->string('original_url', 255)->nullable();
            $table->string('small_url', 255)->nullable();
            $table->string('medium_url', 255)->nullable();
            $table->string('big_url', 255)->nullable();
            $table->bigInteger('picturable_id')->nullable();
            $table->string('picturable_type', 255)->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pictures');
    }
}
