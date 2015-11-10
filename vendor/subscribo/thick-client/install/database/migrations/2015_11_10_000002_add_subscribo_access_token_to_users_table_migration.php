<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubscriboAccessTokenToUsersTableMigration extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = 'subscribo_access_token';
            $table->string($columnName)->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = 'subscribo_access_token';
            $table->dropColumn($columnName);
        });
    }
}
