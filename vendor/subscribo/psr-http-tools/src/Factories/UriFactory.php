<?php namespace Subscribo\PsrHttpTools\Factories;

use Psr\Http\Message\UriInterface;
use Phly\Http\Uri;

/**
 * Class UriFactory
 *
 * @package Subscribo\PsrHttpTools
 */
class UriFactory
{
    /**
     * Makes PSR-7 compliant Uri object
     *
     * @param string|UriInterface $uri
     * @param array $queryParameters Query parameters to add / overwrite
     * @return UriInterface
     */
    public static function make($uri, array $queryParameters = [])
    {
        $uri = ($uri instanceof UriInterface) ? $uri : new Uri($uri);
        if ($queryParameters) {
            $uri = static::addQueryParameters($uri, $queryParameters);
        }
        return $uri;
    }


    /**
     * Merges additional query parameters with current query parameters (possibly overwriting (some of) them)
     *
     * @param UriInterface $uri
     * @param array $queryParameters Query parameters to add / overwrite
     * @return UriInterface
     */
    public static function addQueryParameters(UriInterface $uri, array $queryParameters)
    {
        $originalQuery = $uri->getQuery();
        $originalQueryParameters = [];
        parse_str($originalQuery, $originalQueryParameters);
        $newQueryParameters = array_replace_recursive($originalQueryParameters, $queryParameters);
        $newQuery = http_build_query($newQueryParameters);
        $newUri = $uri->withQuery($newQuery);
        return $newUri;
    }
}
