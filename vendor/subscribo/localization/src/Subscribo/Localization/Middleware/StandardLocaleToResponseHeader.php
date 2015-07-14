<?php namespace Subscribo\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

/**
 * Class StandardLocaleToResponseHeader
 *
 * @package Subscribo\Localization
 */
class StandardLocaleToResponseHeader
{
    /** @var LocalizerInterface  */
    protected $localizer;

    /** @var \Psr\Log\LoggerInterface  */
    protected $logger;

    public function __construct(LocalizerInterface $localizer, LoggerInterface $logger)
    {
        $this->localizer = $localizer;
        $this->logger = $logger;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if ($response instanceof Response) {
            $response->headers->set('Content-Language', $this->localizer->getStandardLocale());
        } else {
            $this->logger->warning('LocaleToResponseHeader: provided response is not instance of Response');
        }
        return $response;
    }
}
