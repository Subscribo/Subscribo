<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocaleManagerInterface;
use Subscribo\Localization\Deposits\SessionDeposit;

/**
 * Class LocaleFromSession
 *
 * @package Subscribo\Localization
 */
class LocaleFromSession
{
    /** @var LocaleManagerInterface  */
    protected $localeManager;

    /** @var SessionDeposit  */
    protected $deposit;

    public function __construct(LocaleManagerInterface $localeManager, SessionDeposit $deposit)
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
            \Log::notice('Locale not found in session');
            return;
        }
        \Log::notice('Locale from session:'. $locale);
        $this->localeManager->setLocale($locale);
    }
}
