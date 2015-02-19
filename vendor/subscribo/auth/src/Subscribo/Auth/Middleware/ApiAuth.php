<?php namespace Subscribo\Auth\Middleware;

use Closure;
use Subscribo\Auth\Interfaces\StatelessGuardInterface;
use Subscribo\RestCommon\Exceptions\UnauthorizedHttpException;
use Illuminate\Http\Request;

class ApiAuth
{
    /**
     * @var \Subscribo\Auth\Interfaces\StatelessGuardInterface
     */
    protected $auth;

    public function __construct(StatelessGuardInterface $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->auth->guest()) {
            throw new UnauthorizedHttpException();
        }
        return $next($request);
    }
}
