<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Bank
 */
class CreateBanksTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('bic', 255);
            $table->string('bank_code', 255)->nullable();
            $table->integer('country_id')->unsigned();
            $table->integer('address_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('country_id', 'banks_country_id_foreign')->references('id')->on('countries');
            $table->foreign('address_id', 'banks_address_id_foreign')->references('id')->on('addresses');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
    }
}
