<?php namespace Subscribo\Localization;

/**
 * Class LocaleTools
 * Helper class to manipulate/transform locale tags
 *
 * @package Subscribo\Localization
 */
class LocaleTools
{
    /**
     * Returns standard part of locale tag, in BCP 47 format
     *
     * @param string $localeTag
     * @param bool $simplify whether to simplify tags where country and language are same (in different case)
     * @return string
     */
    public static function localeTagToBCP($localeTag, $simplify = false)
    {
        $standard = static::localeTagToStandard($localeTag);
        $parts = explode('_', $standard);
        $language = strtolower(reset($parts));
        if (empty($parts[1])) {
            return $language;
        }
        $country = strtoupper($parts[1]);
        if ($simplify and ($language === strtolower($country))) {
            return $language;
        }
        return $language.'-'.$country;
    }

    /**
     * Returns standard (POSIX) part of possibly custom locale tag
     *
     * @param string $localeTag
     * @return string
     */
    public static function localeTagToStandard($localeTag)
    {
        $parts = explode('-', $localeTag);
        return reset($parts);
    }

    /**
     * Returns language part of locale tag
     *
     * @param string $localeTag
     * @return string
     */
    public static function localeTagToLanguage($localeTag)
    {
        $standard = static::localeTagToStandard($localeTag);
        $parts = explode('_', $standard);
        $languagePart = reset($parts);
        $language = strtolower($languagePart);
        return $language;
    }

    /**
     * Extracts first locale tag from provided source string
     *
     * @param string $source
     * @return bool|string
     */
    public static function extractFirstLocaleTag($source)
    {
        if (empty($source)) {
            return false;
        }
        $matches = [];
        if ( ! preg_match('/^[a-zA-Z0-9_-]+/', trim($source), $matches)) {
            return false;
        }
        return $matches[0];
    }

}
