<?php namespace Subscribo\ApiClientLocalization;

use Subscribo\Localization\Interfaces\LocaleSettingsManagerInterface;
use Subscribo\Localization\StaticLocaleSettingsManager;

/**
 * Class LocalePossibilities
 *
 * @package Subscribo\ApiClientLocalization
 */
class LocalePossibilities extends StaticLocaleSettingsManager implements LocaleSettingsManagerInterface
{

    public function getLocaleByUriStub($uriStub)
    {
        if (empty($this->data['uriStubs'][$uriStub])) {
            return false;
        }
        return $this->data['uriStubs'][$uriStub];
    }

    public function getAvailableUriStubs()
    {
        return array_keys($this->data['uriStubs']);
    }

    public function getAvailableLocalesWithUriStub()
    {
        return $this->data['uriStubs'];
    }

    public function getLabel($identifier, $locale = null, $default = true)
    {
        if (is_null($locale)) {
            $locale = $identifier;
        }
        if (empty($this->data['labels'][$identifier][$locale])) {
            $locale = $identifier;
        }
        if (empty($this->data['labels'][$identifier][$locale])) {
            return ($default === true) ? $identifier : $default;
        }
        return $this->data['labels'][$identifier][$locale];
    }
}
