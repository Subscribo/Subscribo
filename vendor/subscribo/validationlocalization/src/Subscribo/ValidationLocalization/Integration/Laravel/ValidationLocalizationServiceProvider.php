<?php namespace Subscribo\ValidationLocalization\Integration\Laravel;

use Illuminate\Validation\Factory;
use Subscribo\Support\ServiceProvider;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;

/**
 * Class ValidationLocalizationServiceProvider
 *
 * @package Subscribo\ValidationLocalization
 */
class ValidationLocalizationServiceProvider extends ServiceProvider
{
    public function register()
    {
        // We need to register Illuminate\Validation\ValidationServiceProvider first,
        // so that it would not be registered again due to its listing in config/app.php - providers
        // and not override our custom validator bindings
        $this->app->register('\\Illuminate\\Validation\\ValidationServiceProvider');

        $this->app->register('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider');

        $this->app->singleton('subscribo.validationlocalization.localizer', function ($app) {
            /** @var LocalizationManagerInterface $manager */
            $manager = $app->make('subscribo.localization.manager');
            /** @var LocalizerInterface $localizer */
            $localizer = $manager->localizer('validationlocalization', 'validation');
            return $localizer;
        });
        // We are overriding validator binding from Illuminate\Validation\ValidationServiceProvider
        $this->app->singleton('validator', function ($app) {
            $validator = new Factory($app->make('subscribo.validationlocalization.localizer'), $app);
            if (isset($app['validation.presence'])) {
                $validator->setPresenceVerifier($app['validation.presence']);
            }
            return $validator;
        });
    }

    public function boot()
    {
        $this->registerTranslationResources('validation');
    }

}
