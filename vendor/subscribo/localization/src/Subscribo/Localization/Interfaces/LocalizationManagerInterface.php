<?php namespace Subscribo\Localization\Interfaces;

use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class LocalizationManagerInterface
 *
 * @package Subscribo\Localization
 */
interface LocalizationManagerInterface
{

    /**
     * Translate line
     *
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function trans($id, array $parameters, $domain, $locale);

    /**
     * Translate line with choices (usually for pluralization)
     *
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function transChoice($id, $number, array $parameters, $domain, $locale);

    /**
     * Generates new Localizer
     *
     * @param string|null $namespace
     * @param string|null $subdomain
     * @return LocalizerInterface
     */
    public function localizer($namespace = null, $subdomain = null);

    /**
     * @param string $locale
     * @return array
     */
    public function fallbackLocales($locale);

    /**
     * @return array
     */
    public function availableLocales();

}
