<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\CurrencyPossibilitiesPerCountry;

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
        //Then we try to find available locale with the same standard part
        $preferredStandard = strstr($preferred, '-', true) ?: $preferred;
        foreach ($this->availableLocales as $locale) {
            if ($preferredStandard === $locale->extractStandardPart()) {
                return $locale;
            }
        }
        //Then we try to find available locale with the same language part
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
            return 0;
        }
        if (empty($customerPreference)) {
            return 0;
        }
        $result =  min($customerPreference, $this->rememberLocale);
        return $result;
    }

    public function addCountries($countries, $currencies)
    {
        $countries = is_array($countries) ? $countries : [$countries];
        $currencies = is_array($currencies) ? $currencies : [$currencies];
        foreach ($countries as $country) {
            if (is_null($this->defaultCountryId)) {
                $this->defaultCountry()->associate($country);
                $this->save();
            }
            $this->availableCountries()->attach($country);
            $defaultCurrency = true;
            foreach ($currencies as $currency) {
                $possibility =  new CurrencyPossibilitiesPerCountry();
                $possibility->serviceId = $this->id;
                $possibility->currencyId = $currency->id;
                $possibility->countryId = $country->id;
                $possibility->isDefault = $defaultCurrency;
                $possibility->save();
                $defaultCurrency = false;
            }
        }
    }

    public function provideAvailableCurrencyIds($countryId = null)
    {
        return CurrencyPossibilitiesPerCountry::provideCurrencyIdsForServiceIdAndCountryId($this->id, $countryId);
    }

    public function provideDefaultCurrencyId($countryId = null)
    {
        return CurrencyPossibilitiesPerCountry::provideDefaultCurrencyIdForServiceIdAndCountryId($this->id, $countryId);
    }

    /**
     * @param int|Country $countryId
     * @return bool
     */
    public function isOperatingInCountry($countryId)
    {
        if ($countryId instanceof Country) {
            $countryId = $countryId->id;
        } else {
            $countryId = intval($countryId);
        }
        foreach ($this->availableCountries as $availableCountry) {
            if ($availableCountry->id === $countryId) {
                return true;
            }
        }
        return false;
    }
}
