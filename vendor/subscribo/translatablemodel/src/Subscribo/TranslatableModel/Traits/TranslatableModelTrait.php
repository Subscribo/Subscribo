<?php namespace Subscribo\TranslatableModel\Traits;

use Subscribo\TranslatableModel\Exceptions\NotImplementedException;
use Subscribo\TranslatableModel\Interfaces\LocaleConfigurationInterface;
use Illuminate\Database\Eloquent\Builder;
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


    public function hasTranslation($locale = null)
    {
        $locale = $locale ?: $this->getLocaleConfiguration()->getCurrentLocale();

        foreach ($this->translations as $translation) {
            if ($translation->getAttribute($this->getLocaleKey()) == $locale) {
                return true;
            }
        }
        return false;
    }


    public function setAttribute($key, $value)
    {
        if ($this->isFieldTranslatable($key)) {
            $locale = $this->getLocaleConfiguration()->getCurrentLocale();
            $this->translateOrNew($locale)->$key = $value;
        } else {
            parent::setAttribute($key, $value);
        }
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isFieldTranslatable($fieldName)
    {
        return in_array($fieldName, $this->translatedAttributes, true);
    }

    /**
     * @param string $fieldName
     * @param string|null $locale
     * @param bool|null $withFallback
     * @return mixed|null
     */
    public function translateIfTranslatable($fieldName, $locale = null, $withFallback = null)
    {
        if ( ! $this->isFieldTranslatable($fieldName)) {
            return $this->$fieldName;
        }
        $translation = $this->getTranslation($locale, $withFallback);
        return $translation ? $translation->$fieldName : null;
    }

    /**
     * @todo implement properly using array of fallback translations
     *
     * @param Builder $query
     * @param string $translationField
     * @throws NotImplementedException
     */
    public function scopeListsTranslations(Builder $query, $translationField)
    {
        //todo implement properly using array of fallback translations
        throw new NotImplementedException('TranslatableModelTrait: method scopeListsTranslations() is not implemented yet');
    }

    public function getLocales()
    {
        return $this->getLocaleConfiguration()->getAvailableLocales();
    }

}
