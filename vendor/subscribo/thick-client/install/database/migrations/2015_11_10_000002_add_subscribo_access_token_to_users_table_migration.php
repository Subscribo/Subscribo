<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubscriboAccessTokenToUsersTableMigration extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = $this->getAccessTokenColumnName();
            $table->string($columnName)->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $columnName = $this->getAccessTokenColumnName();
            $table->dropColumn($columnName);
        });
    }

    /**
     * @return string
     */
    protected function getAccessTokenColumnName()
    {
        /** @var \Subscribo\ThickClientIntegration\Managers\ThickClientIntegrationManager $manager */
        $manager = app('\\Subscribo\\ThickClientIntegration\\Managers\\ThickClientIntegrationManager');

        return Str::snake($manager->getAccessTokenAttributeName());
    }
}
