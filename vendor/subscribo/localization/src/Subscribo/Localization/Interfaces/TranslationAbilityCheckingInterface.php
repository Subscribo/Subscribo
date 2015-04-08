<?php namespace Subscribo\Localization\Interfaces;

/**
 * Interface TranslationAbilityCheckingInterface
 *
 * @package Subscribo\Localization
 */
interface TranslationAbilityCheckingInterface
{
    const CAN_TRANSLATE_MODE_WITHOUT_FALLBACK = 'CAN_TRANSLATE_MODE_WITHOUT_FALLBACK';
    const CAN_TRANSLATE_MODE_SAME_LANGUAGE = 'CAN_TRANSLATE_MODE_SAME_LANGUAGE';
    const CAN_TRANSLATE_MODE_ANY_LOCALE = 'CAN_TRANSLATE_MODE_ANY_LOCALE';

    /**
     * Checks whether is able to translate provided id within given domain
     *
     * @param string $id
     * @param string $domain
     * @param string|null $locale
     * @param string|null $mode
     * @return bool|null
     */
    public function canTranslate($id, $domain = null, $locale = null, $mode = null);

}
