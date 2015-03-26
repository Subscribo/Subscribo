<?php namespace Subscribo\Localization\Integration\Laravel;

use Closure;
use RuntimeException;
use Subscribo\Localization\Localizer;
use Subscribo\Localization\StaticLocaleSettingsManager;
use Subscribo\Localization\Interfaces\LocaleSettingsManagerInterface;
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
        $this->app->singleton('subscribo.localization.resources.manager', '\\Subscribo\\Localization\\LocalizationResourcesManager');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizationResourcesManagerInterface', 'subscribo.localization.resources.manager');
        $this->app->singleton('subscribo.localization.manager', '\\Subscribo\\Localization\\LocalizationManager');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizationManagerInterface', 'subscribo.localization.manager');
        $this->app->singleton('subscribo.localizer', function ($app) {
            return new Localizer($app->make('subscribo.localization.manager'), $app->make('config')->get('app.locale'));
        });
        $this->app->singleton('\\Subscribo\\Localization\\Localizer', 'subscribo.localizer');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizerInterface', 'subscribo.localizer');
    }

    public function boot()
    {
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\\Localizer', '\\Subscribo\\Localization\\Integration\\Laravel\\Facades\\Localizer');
    }

    /**
     * @param array|Closure $data
     * @param null|Closure|array|string $defaultFallbackLocales
     * @param string $className
     * @param bool|null $asInterface
     */
    public function registerLocaleSettingsManager($data, $defaultFallbackLocales = null, $className = '\\Subscribo\\Localization\\StaticLocaleSettingsManager', $asInterface = null)
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
