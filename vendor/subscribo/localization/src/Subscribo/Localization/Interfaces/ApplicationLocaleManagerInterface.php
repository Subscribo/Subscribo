<?php namespace Subscribo\Localization\Interfaces;

use Subscribo\Localization\Interfaces\HavingLocaleInterface;

interface ApplicationLocaleManagerInterface extends HavingLocaleInterface
{
    const FORMAT_NONE = false;
    const FORMAT_STANDARD = 'standard';
    const FORMAT_FULL = 'full';
    const FORMAT_LANGUAGE = 'language';

    /**
     * Sets locale for both application wide LocaleManager as well as for Laravel application
     *
     * @param string|bool $locale - true for getting locale from application wide LocaleManager
     * @param string|bool $applicationLocaleFormat false for not setting locale for Laravel application
     * @return void
     */
    public function setLocale($locale = true, $applicationLocaleFormat = self::FORMAT_STANDARD);

}
