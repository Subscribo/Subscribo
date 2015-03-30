<?php namespace Subscribo\TranslatableModel\Interfaces;

/**
 * Class LocaleConfigurationInterface
 *
 * This interface need to be implemented by package/project using package subscribo/translatablemodel
 *
 * @package Subscribo\TranslatableModel
 */
interface LocaleConfigurationInterface
{
    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     * @return array
     */
    public function getFallbackLocales($locale);

}
