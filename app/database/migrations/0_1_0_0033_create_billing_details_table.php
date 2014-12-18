<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\BillingDetail
 */
class CreateBillingDetailsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_details', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->tinyInteger('type')->default(1);
            $table->integer('address_id')->unsigned()->nullable();
            $table->string('account_no', 255)->nullable();
            $table->integer('bank_id')->unsigned()->nullable();
            $table->string('iban', 255)->nullable();
            $table->string('bic', 255)->nullable();
            $table->timestamps();
            $table->foreign('address_id', 'billing_details_address_id_foreign')->references('id')->on('addresses');
            $table->foreign('bank_id', 'billing_details_bank_id_foreign')->references('id')->on('banks');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_details');
    }
}
