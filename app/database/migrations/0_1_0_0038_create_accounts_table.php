<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Account
 */
class CreateAccountsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('service_id')->unsigned();
            $table->timestamps();
            $table->foreign('user_id', 'accounts_user_id_foreign')->references('id')->on('users');
            $table->foreign('service_id', 'accounts_service_id_foreign')->references('id')->on('services');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
