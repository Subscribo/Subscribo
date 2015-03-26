<?php namespace Subscribo\Localization\Translators;

/**
 * Class Translator
 * Extending Symfony Translation Component Translator class and modifying some functionality
 * Contain code / ideas taken from Symfony Component Translator class and modified
 *
 * @license MIT
 * @package Subscribo\Localization
 */
class Translator extends \Symfony\Component\Translation\Translator
{
    /**
     * Method computing (filtering) fallback locales
     * As opposed to parent function does not prepend generic language tag
     * @param string $locale
     * @return array
     */
    public function computeFallbackLocales($locale)
    {
        $computed = array_filter(
            $this->getFallbackLocales(),
            function ($value) use ($locale) {return $locale !== $value;}
        );
        $unique = array_unique($computed);
        return $unique;
    }

}
