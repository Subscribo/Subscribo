<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates pivot table for simple many to many relation
 * Related tables: service_pools, services
 */
class CreateServiceServicePoolTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_service_pool', function(Blueprint $table)
        {
            $table->bigIncrements('id')->unsigned();
            $table->integer('service_pool_id')->unsigned();
            $table->integer('service_id')->unsigned();
            $table->bigInteger('ordering')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('service_pool_id', 'service_service_pool_service_pool_id_foreign')->references('id')->on('service_pools')->onDelete('cascade');
            $table->foreign('service_id', 'service_service_pool_service_id_foreign')->references('id')->on('services')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_service_pool');
    }
}
