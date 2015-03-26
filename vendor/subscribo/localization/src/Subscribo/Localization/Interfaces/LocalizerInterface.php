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
     * @param string $description
     * @return string
     */
    public static function parseLocaleDescription($description);

    /**
     * @param string $subdomain
     * @param string|null $namespace
     * @param string|null $locale
     * @return $this
     */
    public function setup($subdomain, $namespace = null, $locale = null);

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
     * @return string
     */
    public function getStandardLocale();
}
