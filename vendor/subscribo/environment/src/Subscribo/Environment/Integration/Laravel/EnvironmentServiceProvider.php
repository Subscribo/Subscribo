<?php namespace Subscribo\Environment\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Subscribo\Environment\EnvironmentRegistry;


/**
 * Class EnvironmentServiceProvider
 *
 * @package Subscribo\Environment
 */
class EnvironmentServiceProvider extends ServiceProvider {

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected $defer = false;

    public function register()
    {
        $environmentInstance = EnvironmentRegistry::getInstance();

        $this->setEnvFile($environmentInstance->getEnvironment());

        $this->app->instance('subscribo.environment', $environmentInstance);
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\\Environment', 'Subscribo\\Environment\\Integration\\Laravel\\Facades\\Environment');
    }

    protected function setEnvFile($environment)
    {
        if (empty($environment)) {
            return;
        }
        $environmentFileName = '.env.'.$environment;
        $environmentFilePath = $this->app->basePath().'/'.$environmentFileName;
        if ( ! file_exists($environmentFilePath)) {
            return;
        }
        $this->app->loadEnvironmentFrom($environmentFileName);
    }
}
