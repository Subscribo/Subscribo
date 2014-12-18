<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates pivot table for simple many to many relation
 * Related tables: acl_rights, acl_roles
 */
class CreateAclRightAclRoleTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_right_acl_role', function(Blueprint $table)
        {
            $table->bigIncrements('id')->unsigned();
            $table->integer('acl_right_id')->unsigned();
            $table->integer('acl_role_id')->unsigned();
            $table->bigInteger('ordering')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('acl_right_id', 'acl_right_acl_role_acl_right_id_foreign')->references('id')->on('acl_rights')->onDelete('cascade');
            $table->foreign('acl_role_id', 'acl_right_acl_role_acl_role_id_foreign')->references('id')->on('acl_roles')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_right_acl_role');
    }
}
