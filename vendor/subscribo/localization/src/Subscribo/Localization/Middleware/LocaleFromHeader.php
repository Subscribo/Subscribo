<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocalizerInterface;

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
        $matches = [];
        if ( ! preg_match('/^[a-zA-Z0-9_-]+/', trim($headerContent), $matches)) {
            \Log::notice('Locale not found in header');
            return;
        }
        \Log::notice('Locale from header', $matches);
        $this->localizer->setLocale($matches[0]);
    }

}
