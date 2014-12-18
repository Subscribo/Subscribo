<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates pivot table for simple many to many relation
 * Related tables: users, acl_groups
 */
class CreateAclGroupUserTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_group_user', function(Blueprint $table)
        {
            $table->bigIncrements('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('acl_group_id')->unsigned();
            $table->bigInteger('ordering')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('user_id', 'acl_group_user_user_id_foreign')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('acl_group_id', 'acl_group_user_acl_group_id_foreign')->references('id')->on('acl_groups')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_group_user');
    }
}
