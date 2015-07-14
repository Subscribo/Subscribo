<?php namespace Subscribo\DevelopmentSeeder\Integration\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class DevelopmentSeederServiceProvider
 *
 * @package Subscribo\DevelopmentSeeder
 */
class DevelopmentSeederServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\ModelCore\\Integration\\Laravel\\ModelCoreServiceProvider');
        $this->app->register('\\Subscribo\\RestCommon\\Integration\\Laravel\\CommonSecretServiceProvider');
    }

    public function boot()
    {
        $packagePath = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $this->publishes([$packagePath.'/install/laravel/database/seeds/DatabaseSeeder.php' => base_path('database/seeds/DatabaseSeeder.php')], 'seeds');
    }
}
