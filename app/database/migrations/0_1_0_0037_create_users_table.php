<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\User
 */
class CreateUsersTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('username', 255);
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->enum('type', array('guest', 'customer', 'administrator', 'superadmin'));
            $table->string('remember_token', 255)->nullable();
            $table->boolean('email_confirmed')->nullable();
            $table->string('oauth', 255)->nullable();
            $table->string('fb_account', 255)->nullable();
            $table->integer('person_id')->unsigned()->nullable();
            $table->integer('default_delivery_address_id')->unsigned()->nullable();
            $table->integer('default_billing_details_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('person_id', 'users_person_id_foreign')->references('id')->on('persons');
            $table->foreign('default_delivery_address_id', 'users_default_delivery_address_id_foreign')->references('id')->on('addresses');
            $table->foreign('default_billing_details_id', 'users_default_billing_details_id_foreign')->references('id')->on('billing_details');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
