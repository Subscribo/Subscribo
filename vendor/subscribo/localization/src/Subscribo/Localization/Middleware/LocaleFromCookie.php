<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Deposits\CookieDeposit;

/**
 * Class LocaleFromCookie
 *
 * @package Subscribo\Localization
 */
class LocaleFromCookie
{
    /** @var LocalizerInterface  */
    protected $localizer;

    /** @var \Subscribo\Localization\Deposits\CookieDeposit  */
    protected $deposit;

    public function __construct(LocalizerInterface $localizer, CookieDeposit $deposit)
    {
        $this->localizer = $localizer;
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
        $this->localizer->setLocale($locale);
    }
}
