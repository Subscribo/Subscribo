<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class LocaleToApp
 *
 * @package Subscribo\Localization
 */
class LocaleToApp
{
    /** @var LocalizerInterface  */
    protected $localizer;

    /** @var \Illuminate\Contracts\Foundation\Application  */
    protected $app;

    public function __construct(LocalizerInterface $localizer, Application $app)
    {
        $this->localizer = $localizer;
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->app->setLocale($this->localizer->getLocale());
        return $next($request);
    }
}
