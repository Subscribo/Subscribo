<?php namespace Subscribo\Api1\Middleware;

use Closure;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Logging
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->logger->debug('Request: ', $this->requestToArray($request));
        $result = $next($request);
        $this->logger->debug('Response: ', $this->responseToArray($result));
        return $result;
    }

    protected function requestToArray(Request $request)
    {
        $result = [
            'url'       => $request->getRequestUri(),
            'method'    => $request->getMethod(),
            'content'   => $request->getContent(),
        ];
        return $result;
    }

    protected function responseToArray($response)
    {
        if ($response instanceof Response) {
            $result = [
                'statusCode'    => $response->getStatusCode(),
                'content'       => $response->getContent(),
            ];
            return $result;
        }
        return (array) $response;
    }


}
