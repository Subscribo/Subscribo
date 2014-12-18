<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for model \Model\AclRight
 */
class CreateAclRightsTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_rights', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('identifier', 255);
            $table->string('name', 255);
            $table->string('comment', 255)->nullable();
            $table->integer('api_method_id')->unsigned();
            $table->timestamps();
            $table->foreign('api_method_id', 'acl_rights_api_method_id_foreign')->references('id')->on('api_methods');
            $table->unique('identifier');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_rights');
    }
}
