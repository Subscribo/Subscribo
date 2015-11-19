<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddSubscriboClientTokenToUsersTableMigration extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = $this->getClientTokenColumnName();
            $table->string($columnName)->unique()->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = $this->getClientTokenColumnName();
            $uniqueIndexName = 'users_'.$columnName.'_unique';
            $table->dropUnique($uniqueIndexName);
            $table->dropColumn($columnName);
        });
    }

    /**
     * @return string
     */
    protected function getClientTokenColumnName()
    {
        /** @var \Subscribo\ThickClientIntegration\Managers\ThickClientIntegrationManager $manager */
        $manager = app('\\Subscribo\\ThickClientIntegration\\Managers\\ThickClientIntegrationManager');

        return Str::snake($manager->getClientTokenAttributeName());
    }
}
