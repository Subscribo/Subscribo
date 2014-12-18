<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Api
 */
class CreateApisTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apis', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('name', 255);
            $table->string('comment', 255)->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
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
        Schema::dropIfExists('apis');
    }
}
