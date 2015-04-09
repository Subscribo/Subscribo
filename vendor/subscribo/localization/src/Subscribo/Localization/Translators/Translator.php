<?php namespace Subscribo\Localization\Translators;

use Subscribo\Localization\LocaleTools;
use Subscribo\Localization\Interfaces\TranslationAbilityCheckingInterface;

/**
 * Class Translator
 * Extending Symfony Translation Component Translator class and modifying some functionality
 * Contain code / ideas taken from Symfony Component Translator class and modified
 *
 * @license MIT
 * @package Subscribo\Localization
 */
class Translator extends \Symfony\Component\Translation\Translator implements TranslationAbilityCheckingInterface
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

    /**
     * Check whether it is possible to translate specified id
     *
     * @param string $id
     * @param string|null $domain
     * @param string|null $locale
     * @param string|null $mode
     * @return bool
     */
    public function canTranslate($id, $domain = null, $locale = null, $mode = self::CAN_TRANSLATE_MODE_SAME_LANGUAGE)
    {
        $mode = $mode ?: self::CAN_TRANSLATE_MODE_SAME_LANGUAGE;

        if (is_null($domain)) {
            $domain = 'messages';
        }
        $id = (string) $id;
        $locale = $this->prepareLocaleAndCatalogues($locale);
        $language = LocaleTools::localeTagToLanguage($locale);
        $catalogue = $this->getCatalogue($locale);
        if ((self::CAN_TRANSLATE_MODE_WITHOUT_FALLBACK === $mode) and ( ! $catalogue->defines($id, $domain))) {
            return false;
        }
        while ($catalogue) {
            if ($catalogue->defines($id, $domain)) {
                if (self::CAN_TRANSLATE_MODE_ANY_LOCALE === $mode) {
                    return true;
                }
                $catalogueLanguage = LocaleTools::localeTagToLanguage($catalogue->getLocale());
                if ((self::CAN_TRANSLATE_MODE_SAME_LANGUAGE === $mode) and ($language === $catalogueLanguage)) {
                    return true;
                }
            }
            $catalogue = $catalogue->getFallbackCatalogue();
        }
        return false;
    }

    /**
     * @param string $locale
     * @return string
     */
    protected function prepareLocaleAndCatalogues($locale)
    {
        if (is_null($locale)) {
            $locale = $this->getLocale();
        } else {
            $this->assertValidLocale($locale);
        }
        if ( ! isset($this->catalogues[$locale])) {
            $this->loadCatalogue($locale);
        }
        return $locale;
    }

}
