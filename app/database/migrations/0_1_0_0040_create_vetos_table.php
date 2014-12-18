<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\Veto
 */
class CreateVetosTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vetos', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->date('start');
            $table->date('end');
            $table->integer('subscription_id')->unsigned();
            $table->timestamps();
            $table->foreign('subscription_id', 'vetos_subscription_id_foreign')->references('id')->on('subscriptions');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vetos');
    }
}
