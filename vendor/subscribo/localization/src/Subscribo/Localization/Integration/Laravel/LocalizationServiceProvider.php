<?php namespace Subscribo\Localization\Integration\Laravel;

use Closure;
use RuntimeException;
use Subscribo\Localization\Localizers\Localizer;
use Subscribo\Localization\Managers\StaticLocaleSettingsManager;
use Subscribo\Localization\Managers\LocaleManager;
use Subscribo\Localization\Interfaces\LocaleSettingsManagerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;
use Subscribo\Localization\Interfaces\LocalizationResourcesManagerInterface;
use Subscribo\Localization\Interfaces\LocaleManagerInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

/**
 * Class LocalizationServiceProvider
 *
 * @package Subscribo\Localization
 */
class LocalizationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizationResourcesManagerInterface', '\\Subscribo\\Localization\\Managers\\LocalizationResourcesManager');
        $this->app->singleton('subscribo.localization.manager', '\\Subscribo\\Localization\\Managers\\LocalizationManager');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizationManagerInterface', 'subscribo.localization.manager');

        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocaleManagerInterface', function ($app) {
            return new LocaleManager($app->make('config')->get('app.locale'));
        });

        $this->app->bind('subscribo.localizer', function ($app) {
            /** @var LocalizationManagerInterface $manager */
            $manager = $app->make('subscribo.localization.manager');
            $localizer = $manager->localizer();
            return $localizer;
        });
        $this->app->bind('\\Subscribo\\Localization\\Interfaces\\LocalizerInterface', 'subscribo.localizer');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\ApplicationLocaleManagerInterface', '\\Subscribo\\Localization\\Managers\\ApplicationLocaleManagerInterface');
    }

    public function boot()
    {
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\\Localizer', '\\Subscribo\\Localization\\Integration\\Laravel\\Facades\\Localizer');
        $aliasLoader->alias('Subscribo\\Localization', '\\Subscribo\\Localization\\Integration\\Laravel\\Facades\\Localization');
    }

    /**
     * @param array|Closure $data
     * @param null|Closure|array|string $defaultFallbackLocales
     * @param string $className
     * @param bool|null $asInterface
     */
    public function registerLocaleSettingsManager($data, $defaultFallbackLocales = null, $className = '\\Subscribo\\Localization\\Managers\\StaticLocaleSettingsManager', $asInterface = null)
    {
        $this->app->singleton(
            $className,
            function ($app) use ($data, $defaultFallbackLocales, $className) {
                if ($data instanceof Closure) {
                    $data = $data($app);
                }
                if ($defaultFallbackLocales instanceof Closure) {
                    $defaultFallbackLocales = $defaultFallbackLocales($app);
                }
                if (is_null($defaultFallbackLocales)) {
                    $defaultFallbackLocales = $app->make('config')->get('app.fallback_locale');
                }
                return new $className($data, $defaultFallbackLocales);
            }
        );
        if (false === $asInterface) {
            return;
        }
        $this->registerLocaleSettingsManagerInterface($className, $asInterface);
    }

    /**
     * @param string|mixed $class
     * @param bool|null $force
     */
    public function registerLocaleSettingsManagerInterface($class, $force = null)
    {
        if ($force) {
            $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocaleSettingsManagerInterface', $class);
        } else {
            $this->app->bindIf('\\Subscribo\\Localization\\Interfaces\\LocaleSettingsManagerInterface', $class, true);
        }
    }
}
