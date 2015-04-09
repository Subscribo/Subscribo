<?php namespace Subscribo\Localization;

use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;

/**
 * Class LocaleUtils
 * Utility connected with localization / locale handling
 *
 * @package Subscribo\Localization
 */
class LocaleUtils
{
    public static function rememberLocaleForUser($user, SessionDeposit $sessionDeposit = null, CookieDeposit $cookieDeposit = null)
    {
        if ( ! is_object($user)) {
            return;
        }
        $locale = empty($user->locale) ? false : $user->locale;
        if ( ! $locale) {
            return;
        }
        if ($sessionDeposit) {
            $sessionDeposit->setLocale($locale);
        }
        if ($cookieDeposit and ! empty($user->rememberLocale))  {
            $cookieDeposit->setLocale($locale, $user->rememberLocale);
        }
    }
}
