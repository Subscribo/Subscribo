<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\ApplicationLocaleManagerInterface;

/**
 * Class LocaleToApp
 *
 * @package Subscribo\Localization
 */
class LocaleToApp
{
    /** @var ApplicationLocaleManagerInterface  */
    protected $manager;

    public function __construct(ApplicationLocaleManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->manager->setLocale();
        return $next($request);
    }
}
