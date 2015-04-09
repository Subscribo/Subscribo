<?php namespace Subscribo\Localization\Interfaces;

/**
 * Class HavingLocaleInterface
 *
 * @package Subscribo\Localization
 */
interface HavingLocaleInterface
{
    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     * @return mixed|void
     */
    public function setLocale($locale);
}
