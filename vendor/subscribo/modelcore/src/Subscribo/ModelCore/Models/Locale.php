<?php namespace Subscribo\ModelCore\Models;


/**
 * Model Locale
 *
 * Model class for being changed and used in the application
 */
class Locale extends \Subscribo\ModelCore\Bases\Locale
{
    const TYPE_STANDARD = 'standard';
    const TYPE_GENERIC = 'generic';
    const TYPE_CUSTOMIZED = 'customized';


    /**
     * @return string
     */
    public function extractCountry()
    {
        $standardPart = $this->extractStandardPart();
        $country = trim(strstr($standardPart, '_', false), '_');
        $result = strtoupper($country);
        return $result;
    }

    /**
     * @return string
     */
    public function extractLanguage()
    {
        $standardPart = $this->extractStandardPart();
        $language = strstr($standardPart, '_', true) ?: $standardPart;
        $result = strtolower($language);
        return $result;
    }

    /**
     * @return string
     */
    public function extractStandardPart()
    {
        $identifier = $this->identifier;
        $standardPart = strstr($identifier, '-', true) ?: $identifier;
        return $standardPart;
    }

    /**
     * @return Locale
     */
    public function uncustomize()
    {
        if ($this->type !== static::TYPE_CUSTOMIZED) {
            return $this;
        }
        foreach ($this->fallbackLocales as $fallbackLocale) {
            if ($fallbackLocale->type !== static::TYPE_CUSTOMIZED) {
                return $fallbackLocale;
            }
        }
        return $this;
    }



}
