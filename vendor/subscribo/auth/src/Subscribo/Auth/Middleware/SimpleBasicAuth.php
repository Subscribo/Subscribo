<?php namespace Subscribo\Auth\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Subscribo\Auth\Guards\SimpleGuard;

class SimpleBasicAuth {

    /**
     * @var \Subscribo\Auth\Guards\SimpleGuard
     */
    protected $auth;

    public function __construct(SimpleGuard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $this->auth->onceBasic();
        if ($response) {
            return $response;
        }
        return $next($request);
    }

}
