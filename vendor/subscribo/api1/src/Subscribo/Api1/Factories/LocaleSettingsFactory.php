<?php namespace Subscribo\Api1\Factories;

use Subscribo\Localization\Interfaces\LocaleSettingsManagerInterface;
use Subscribo\ModelCore\Models\Locale;

/**
 * Class LocaleSettingsFactory
 *
 * @package Subscribo\Api1
 */
class LocaleSettingsFactory implements LocaleSettingsManagerInterface
{
    protected $cachedModels = array();

    protected $cachedFallbackLocales = array();

    protected $cachedAvailableLocales = null;

    protected $defaultFallbackLocales = array();

    public function __construct($data = null, $defaultFallbackLocales = null)
    {
        $this->defaultFallbackLocales = empty($defaultFallbackLocales) ? array() : ((array) $defaultFallbackLocales);
    }

    public function getFallbackLocales($localeIdentifier)
    {
        if (array_key_exists($localeIdentifier, $this->cachedFallbackLocales)) {
            return $this->cachedFallbackLocales[$localeIdentifier];
        }
        $model = $this->getLocaleModel($localeIdentifier);
        if (empty($model)) {
            return ($this->cachedFallbackLocales[$localeIdentifier] = $this->defaultFallbackLocales);
        }
        $fallbackLocales = array();
        foreach ($model->fallbackLocales as $fallbackLocaleModel) {
            $fallbackLocaleIdentifier = $fallbackLocaleModel->identifier;
            $fallbackLocales[] = $fallbackLocaleIdentifier;
            if ( ! array_key_exists($fallbackLocaleIdentifier, $this->cachedModels)) {
                $this->cachedModels[$fallbackLocaleIdentifier] = $fallbackLocaleModel;
            }
        }
        return ($this->cachedFallbackLocales[$localeIdentifier] = $fallbackLocales);
    }

    public function getAvailableLocales()
    {
        if (is_null($this->cachedAvailableLocales)) {
            $this->loadAvailableLocales();
        }
        return $this->cachedAvailableLocales;

    }

    protected function loadAvailableLocales()
    {
        $models = Locale::with('fallbackLocales')->get();
        $availableLocales = array();
        foreach ($models as $model)
        {
            $localeIdentifier = $model->identifier;
            $this->cachedModels[$localeIdentifier] = $model;
            $availableLocales[] = $localeIdentifier;
        }
        $this->cachedAvailableLocales = $availableLocales;
    }

    /**
     * @param string $localeIdentifier
     * @return Locale|bool
     */
    protected function getLocaleModel($localeIdentifier)
    {
        if (array_key_exists($localeIdentifier, $this->cachedModels)) {
            return $this->cachedModels[$localeIdentifier];
        }
        $model = Locale::with('fallbackLocales')->where(['identifier' => $localeIdentifier])->first();
        if (empty($model)) {
            return ($this->cachedModels[$localeIdentifier] = false);
        }
        return ($this->cachedModels[$localeIdentifier] = $model);
    }


}
