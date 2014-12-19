<?php namespace Subscribo\Modifier\Support\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Subscribo\Modifier\Modifier;


/**
 * Class ModifierServiceProvider
 *
 * @package Subscribo\Modifier
 */
class ModifierServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->bind('subscribo.modifier', function() {
            return new Modifier;
        });
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\Modifier', 'Subscribo\\Modifier\\Support\\Laravel\\Facades\\Modifier');
    }
}
