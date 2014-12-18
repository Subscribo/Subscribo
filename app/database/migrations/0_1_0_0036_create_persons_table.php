<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Person
 */
class CreatePersonsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('salutation', 255)->nullable();
            $table->string('prefix', 255)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('middle_names', 255)->nullable();
            $table->string('infix', 255)->nullable();
            $table->string('last_name', 255);
            $table->string('suffix', 255)->nullable();
            $table->enum('gender', array('man', 'woman'));
            $table->date('date_of_birth')->nullable();
            $table->integer('contact_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('contact_id', 'persons_contact_id_foreign')->references('id')->on('contacts');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persons');
    }
}
