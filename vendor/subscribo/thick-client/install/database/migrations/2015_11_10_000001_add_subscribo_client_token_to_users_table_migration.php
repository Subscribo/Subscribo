<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubscriboClientTokenToUsersTableMigration extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = 'subscribo_client_token';
            $table->string($columnName)->unique()->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = 'subscribo_client_token';
            $uniqueIndexName = 'users_'.$columnName.'_unique';
            $table->dropUnique($uniqueIndexName);
            $table->dropColumn($columnName);
        });
    }
}
