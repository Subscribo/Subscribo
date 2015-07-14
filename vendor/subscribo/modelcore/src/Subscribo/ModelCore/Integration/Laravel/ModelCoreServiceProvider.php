<?php namespace Subscribo\ModelCore\Integration\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class ModelCoreServiceProvider
 *
 * @package Subscribo\ApiClient
 */
class ModelCoreServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\ModelBase\\Integration\\Laravel\\ModelBaseServiceProvider');
    }

    public function boot()
    {
        $packageSrcDir = dirname(dirname(dirname(dirname(__DIR__))));
        $this->publishes([$packageSrcDir.'/modelschema/schema.yml' => base_path('/subscribo/config/packages/schemabuilder/schema.yml')], 'modelschema');
    }

}
