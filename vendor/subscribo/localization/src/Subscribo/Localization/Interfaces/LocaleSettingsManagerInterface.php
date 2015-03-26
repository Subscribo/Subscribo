<?php namespace Subscribo\Localization\Interfaces;

interface LocaleSettingsManagerInterface
{
    /**
     * @param string $localeIdentifier
     * @return array
     */
    public function getFallbackLocales($localeIdentifier);

}
