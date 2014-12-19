<?php namespace Subscribo\ModelBase\Support\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Subscribo\ModelBase\ModelFactory;

/**
 * Class ModelBaseServiceProvider
 *
 * @package Subscribo\ModelBase
 */
class ModelBaseServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->bind('subscribo.modelfactory', function() {
            return new ModelFactory;
        });
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\ModelFactory', 'Subscribo\\ModelBase\\Support\\Laravel\\Facades\\ModelFactory');
    }
}
