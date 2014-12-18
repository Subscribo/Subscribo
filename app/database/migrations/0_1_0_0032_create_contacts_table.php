<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Contact
 */
class CreateContactsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->bigInteger('mobile_phone_number')->unsigned()->nullable();
            $table->bigInteger('landline_phone_number')->unsigned()->nullable();
            $table->integer('home_address_id')->unsigned()->nullable();
            $table->integer('work_address_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('home_address_id', 'contacts_home_address_id_foreign')->references('id')->on('addresses');
            $table->foreign('work_address_id', 'contacts_work_address_id_foreign')->references('id')->on('addresses');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
