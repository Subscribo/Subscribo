<?php namespace Subscribo\Support;

use ReflectionObject;

/**
 * Class ServiceProvider
 * Extending Laravel Framework class and providing some additional functionality
 *
 * @package Subscribo\Support
 */
abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Overload if child class is in non-standard directory
     * @return string
     */
    protected function getPackagePath()
    {
        $reflection = new ReflectionObject($this);
        $fileName = $reflection->getFileName();
        $packagePath = dirname(dirname(dirname(dirname(dirname(dirname($fileName))))));
        return $packagePath;
    }

    /**
     * @param string|array $resources
     * @param string|bool $namespace
     */
    protected function registerTranslationResources($resources, $namespace = true)
    {
        $packagePath = $this->getPackagePath();
        if (true === $namespace) {
            $namespace = basename($packagePath);
        }
        $basePath = $this->app->make('path.base');
        $packageTranslationsPath = $this->getPackagePath().'/resources/lang/';
        $applicationTranslationPath = $basePath.'/subscribo/resources/lang/'.$namespace.'/';
        $this->publishes([$packageTranslationsPath => $applicationTranslationPath], 'translation');

        $manager = $this->app->make('\\Subscribo\\Localization\\Interfaces\\LocalizationResourcesManagerInterface');
        $manager->registerNamespace($namespace, [$packageTranslationsPath, $applicationTranslationPath], $resources);
    }
}
