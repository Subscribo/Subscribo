<?php namespace Subscribo\ModelCore\Models;


/**
 * Model Service - list of websites or other entities using the system
 *
 * Model class for being changed and used in the application
 */
class Service extends \Subscribo\ModelCore\Bases\Service
{
    /**
     * @param string $preferred
     * @return Locale
     */
    public function chooseLocale($preferred)
    {
        $preferred = trim($preferred);
        // If no preferred locale is suggested, we simply return default one
        if (empty($preferred)) {
            return $this->defaultLocale;
        }
        //If we have preferred locale among available locales, we use it
        foreach ($this->availableLocales as $locale) {
            if ($locale->identifier === $preferred) {
                return $locale;
            }
        }
        //Then we try to find available locale with the same language part
        $preferredStandard = strstr($preferred, '-', true) ?: $preferred;
        $preferredLang = strstr($preferredStandard, '_', true) ?: $preferredStandard;
        $preferredLang = strtolower($preferredLang);
        foreach ($this->availableLocales as $locale) {
            if ($preferredLang === $locale->extractLanguage()) {
                return $locale;
            }
        }
        //Then we try to find preferred locale among fallback locales
        foreach ($this->availableLocales as $locale) {
            foreach ($locale->fallbackLocales as $fallbackLocale) {
                if ($preferredLang === $fallbackLocale->extractLanguage()) {
                    return $locale;
                }
            }
        }
        //If everything failed, we just return the default one
        return $this->defaultLocale;
    }

    public function calculateRememberLocale($customerPreference)
    {
        if (empty($this->rememberLocale)) {
            \Log::notice('No for service');
            return 0;
        }
        if (empty($customerPreference)) {
            \Log::notice('No for customer');
            return 0;
        }
        $result =  min($customerPreference, $this->rememberLocale);
        \Log::notice('Result Min:'. $result);
        return $result;
    }

}
