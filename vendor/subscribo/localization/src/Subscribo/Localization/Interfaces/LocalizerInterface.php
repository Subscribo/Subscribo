<?php namespace Subscribo\Localization\Interfaces;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LocalizerInterface
 *
 * @package Subscribo\Localization
 */
interface LocalizerInterface extends TranslatorInterface
{

    /**
     * Whether particular localizer have transparent locale - pointing to current (global) locale
     *
     * @return bool
     */
    public function haveTransparentLocale();

    /**
     * @param string|null $subdomain
     * @param string|null $namespace
     * @param string|null $locale
     * @return static
     */
    public function duplicate($subdomain = null, $namespace = null, $locale = null);

    /**
     * @param string $value
     * @return string
     */
    public function simplify($value);

    /**
     * Returns Standard (POSIX) locale tag (part)
     * @return string
     */
    public function getStandardLocale();

    /**
     * Return Standard locale part converted to BCP 47 format
     * @return string
     */
    public function getBCPLocale();

    /**
     * @param string|null $locale
     * @return array
     */
    public function getFallbackLocales($locale = null);

    /**
     * @return array
     */
    public function getAvailableLocales();

}
