<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Payment
 */
class CreatePaymentsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->tinyInteger('type');
            $table->tinyInteger('status');
            $table->decimal('amount', 10, 2);
            $table->decimal('vat', 10, 2);
            $table->integer('currency_id')->unsigned();
            $table->integer('billing_detail_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('currency_id', 'payments_currency_id_foreign')->references('id')->on('currencies');
            $table->foreign('billing_detail_id', 'payments_billing_detail_id_foreign')->references('id')->on('billing_details');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
