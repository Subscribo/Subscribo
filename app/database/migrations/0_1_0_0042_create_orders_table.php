<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Order
 */
class CreateOrdersTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->enum('type', array('automatic', 'manual'));
            $table->tinyInteger('status');
            $table->integer('payment_id')->unsigned()->nullable();
            $table->integer('delivery_id')->unsigned()->nullable();
            $table->integer('delivery_window_id')->unsigned()->nullable();
            $table->dateTime('anticipated_delivery_start')->nullable();
            $table->dateTime('anticipated_delivery_end')->nullable();
            $table->integer('subscription_id')->unsigned()->nullable();
            $table->integer('account_id')->unsigned()->nullable();
            $table->integer('shipping_address_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('payment_id', 'orders_payment_id_foreign')->references('id')->on('payments');
            $table->foreign('delivery_id', 'orders_delivery_id_foreign')->references('id')->on('deliveries');
            $table->foreign('delivery_window_id', 'orders_delivery_window_id_foreign')->references('id')->on('delivery_windows');
            $table->foreign('subscription_id', 'orders_subscription_id_foreign')->references('id')->on('subscriptions');
            $table->foreign('account_id', 'orders_account_id_foreign')->references('id')->on('accounts');
            $table->foreign('shipping_address_id', 'orders_shipping_address_id_foreign')->references('id')->on('addresses');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
