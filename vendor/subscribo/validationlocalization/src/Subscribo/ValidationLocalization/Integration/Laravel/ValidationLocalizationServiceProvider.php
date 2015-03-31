<?php namespace Subscribo\ValidationLocalization\Integration\Laravel;

use Illuminate\Validation\Factory;
use Subscribo\Support\ServiceProvider;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;
use Subscribo\Localization\Interfaces\LocalizationResourcesManagerInterface;
use Subscribo\Localization\LocaleTools;

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
        $this->registerApplicationValidationLocalizedStrings();
    }

    protected function registerApplicationValidationLocalizedStrings()
    {
        /** @var \Subscribo\Localization\Interfaces\LocalizationResourcesManagerInterface $manager */
        $manager = $this->app->make('\\Subscribo\\Localization\\Interfaces\\LocalizationResourcesManagerInterface');
        $files = glob(base_path('resources/lang').'/*/validation.php');
        foreach ($files as $file) {
            $locale = LocaleTools::extractFirstLocaleTag(basename(dirname($file)));
            $fileContent = $this->getArrayFileContent($file);
            if (empty($locale) or empty($fileContent)) {
                continue;
            }
            $resource = [$locale => ['validation' => $fileContent]];
            $manager->registerResource($resource, 'validationlocalization', 'validation');
        }

    }

    /**
     * @param string $file
     * @return array
     */
    protected function getArrayFileContent($file)
    {
        if ( ! file_exists($file)) {
            return array();
        }
        $result = include($file);
        if ( ! is_array($result)) {
            return array();
        }
        return $result;
    }
}
