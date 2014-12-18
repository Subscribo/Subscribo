<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Table
 */
class CreateTablesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('comment', 255)->nullable();
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
        Schema::dropIfExists('tables');
    }
}
