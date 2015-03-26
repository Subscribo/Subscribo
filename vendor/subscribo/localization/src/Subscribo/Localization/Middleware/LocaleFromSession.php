<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Deposits\SessionDeposit;

/**
 * Class LocaleFromSession
 *
 * @package Subscribo\Localization
 */
class LocaleFromSession
{
    /** @var LocalizerInterface  */
    protected $localizer;

    /** @var \Subscribo\Localization\Deposits\SessionDeposit  */
    protected $deposit;

    public function __construct(LocalizerInterface $localizer, SessionDeposit $deposit)
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
            \Log::notice('Locale not found in session');
            return;
        }
        \Log::notice('Locale from session:'. $locale);
        $this->localizer->setLocale($locale);
    }
}
