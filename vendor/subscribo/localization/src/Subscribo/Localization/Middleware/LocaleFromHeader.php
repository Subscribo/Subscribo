<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class LocaleFromHeader
 *
 * @package Subscribo\Localization
 */
class LocaleFromHeader
{
    /** @var LocalizerInterface  */
    protected $localizer;

    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->setupLocale($request);
        return $next($request);
    }

    protected function setupLocale(Request $request)
    {
        $headerContent = $request->header('Accept-Language');
        $locale = $this->localizer->parseLocaleDescription($headerContent);
        if ( ! $locale) {
            \Log::notice('Locale not found in header');
            return;
        }
        \Log::notice('Locale from header:'. $locale);
        $this->localizer->setLocale($locale);
    }

}
