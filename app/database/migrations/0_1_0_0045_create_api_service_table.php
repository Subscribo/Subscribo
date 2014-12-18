<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates pivot table for simple many to many relation
 * Related tables: apis, services
 */
class CreateApiServiceTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_service', function(Blueprint $table)
        {
            $table->bigIncrements('id')->unsigned();
            $table->integer('api_id')->unsigned();
            $table->integer('service_id')->unsigned();
            $table->bigInteger('ordering')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('api_id', 'api_service_api_id_foreign')->references('id')->on('apis')->onDelete('cascade');
            $table->foreign('service_id', 'api_service_service_id_foreign')->references('id')->on('services')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_service');
    }
}
