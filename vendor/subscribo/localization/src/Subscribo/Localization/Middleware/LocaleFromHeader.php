<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocaleManagerInterface;
use Subscribo\Localization\LocaleTools;

/**
 * Class LocaleFromHeader
 *
 * @package Subscribo\Localization
 */
class LocaleFromHeader
{
    /** @var LocaleManagerInterface  */
    protected $localeManager;

    public function __construct(LocaleManagerInterface $localeManager)
    {
        $this->localeManager = $localeManager;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->setupLocale($request);
        return $next($request);
    }

    protected function setupLocale(Request $request)
    {
        $headerContent = $request->header('Accept-Language');
        $locale = LocaleTools::extractFirstLocaleTag($headerContent);
        if ( ! $locale) {
            \Log::notice('Locale not found in header');
            return;
        }
        \Log::notice('Locale from header:'. $locale);
        $this->localeManager->setLocale($locale);
    }

}
