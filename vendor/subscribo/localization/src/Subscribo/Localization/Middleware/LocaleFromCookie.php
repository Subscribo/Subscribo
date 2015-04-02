<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\ApplicationLocaleManagerInterface;
use Subscribo\Localization\Deposits\CookieDeposit;

/**
 * Class LocaleFromCookie
 *
 * @package Subscribo\Localization
 */
class LocaleFromCookie
{
    /** @var ApplicationLocaleManagerInterface  */
    protected $localeManager;

    /** @var CookieDeposit  */
    protected $deposit;

    public function __construct(ApplicationLocaleManagerInterface $localeManager, CookieDeposit $deposit)
    {
        $this->localeManager = $localeManager;
        $this->deposit = $deposit;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->setupLocale($request);
        return $next($request);
    }

    protected function setupLocale(Request $request)
    {
        $locale = $this->deposit->getLocale();
        if ( ! $locale) {
            \Log::notice('Locale not found in cookie');
            return;
        }
        \Log::notice('Locale from cookie:'. $locale);
        $this->localeManager->setLocale($locale);
    }
}
