<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Subscription
 */
class CreateSubscriptionsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->integer('delivery_window_type_id')->unsigned();
            $table->tinyInteger('period')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->date('start');
            $table->date('end');
            $table->timestamps();
            $table->foreign('account_id', 'subscriptions_account_id_foreign')->references('id')->on('accounts');
            $table->foreign('delivery_window_type_id', 'subscriptions_delivery_window_type_id_foreign')->references('id')->on('delivery_window_types');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
