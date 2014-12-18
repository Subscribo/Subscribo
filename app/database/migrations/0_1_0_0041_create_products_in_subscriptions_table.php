<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\ProductsInSubscription
 */
class CreateProductsInSubscriptionsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_in_subscriptions', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('subscription_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('amount');
            $table->timestamps();
            $table->foreign('subscription_id', 'products_in_subscriptions_subscription_id_foreign')->references('id')->on('subscriptions');
            $table->foreign('product_id', 'products_in_subscriptions_product_id_foreign')->references('id')->on('products');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_in_subscriptions');
    }
}
