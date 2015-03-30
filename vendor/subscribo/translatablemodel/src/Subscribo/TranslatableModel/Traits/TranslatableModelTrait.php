<?php namespace Subscribo\TranslatableModel\Traits;

use Subscribo\TranslatableModel\Interfaces\LocaleConfigurationInterface;
use Dimsav\Translatable\Translatable;

/**
 * Class TranslatableModelTrait
 *
 * Trait to be used in models to help make them internationalized and localized
 *
 * This trait has been created with looking to Dimsav\Translatable\Translatable trait
 *
 * @license MIT
 *
 * @package Subscribo\TranslatableModel
 */
trait TranslatableModelTrait
{
    use Translatable;
    /**
     * @return LocaleConfigurationInterface
     */
    protected function getLocaleConfiguration()
    {
        return app('Subscribo\\TranslatableModel\\Interfaces\\LocaleConfigurationInterface');
    }


    public function getTranslation($locale = null, $withFallback = null)
    {
        $locale = $locale ?: $this->getLocaleConfiguration()->getCurrentLocale();
        $translation = $this->getTranslationByLocaleKey($locale);
        if ($translation) {
            return $translation;
        }
        $withFallback = is_null($withFallback) ? $this->useFallback() : $withFallback;
        if (empty($withFallback)) {
            return null;
        }
        foreach ($this->getLocaleConfiguration()->getFallbackLocales($locale) as $fallbackLocale) {
            $translation = $this->getTranslationByLocaleKey($fallbackLocale);
            if ($translation) {
                return $translation;
            }
        }
        return null;
    }

    public function getLocales()
    {
        return $this->getLocaleConfiguration()->getAvailableLocales();
    }

}
