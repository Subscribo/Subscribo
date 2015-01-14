<?php namespace Subscribo\RestServer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\RestCommon\RestCommon;
use Subscribo\RestCommon\Exceptions\NoAccessTokenHttpException;
use Subscribo\RestCommon\Exceptions\InvalidAccessTokenHttpException;

class CheckAccessToken {

    public function handle(Request $request, Closure $next)
    {
        $token = $request->header(RestCommon::ACCESS_TOKEN_HEADER_FIELD_NAME);
        if (is_null($token)) {
            throw new NoAccessTokenHttpException();
        }
        if (false === strpos($token, 'special')) { //todo: this is not a real implementation of token-based filtering, just an example (all tokens containing string 'special' are considered valid)
                throw new InvalidAccessTokenHttpException();
        }
        return $next($request);
    }

}