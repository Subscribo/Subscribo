<?php namespace Subscribo\Localization\Interfaces;

/**
 * Class LocaleDepositInterface
 *
 * @package Subscribo\Localization
 */
interface LocaleDepositInterface
{
    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param $locale
     * @return void
     */
    public function setLocale($locale);
}
