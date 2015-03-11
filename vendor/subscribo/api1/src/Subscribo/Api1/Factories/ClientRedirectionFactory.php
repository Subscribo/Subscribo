<?php namespace Subscribo\Api1\Factories;

use Subscribo\Api1\Exceptions\RuntimeException;
use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\ServiceModule;
use Subscribo\Support\Str;
use Subscribo\Support\Arr;

/**
 * Class ClientRedirectionFactory
 *
 * @package Subscribo\Api1
 */
class ClientRedirectionFactory
{

    /**
     * @param ClientRedirection|array|string|int $source
     * @param array $additionalData
     * @return ClientRedirection
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public static function make($source, array $additionalData = array())
    {
        if ($source instanceof ClientRedirection) {
            return $source;
        }
        $source = is_string($source) ? json_decode($source, true) : $source;
        $source = is_int($source) ? static::assembleFromCode($source, $additionalData) : $source;
        if ( ! is_array($source)) {
            throw new InvalidArgumentException('ClientRedirectionFactory::make() provided source have incorrect type');
        }
        $clientRedirection = new ClientRedirection($source);
        return $clientRedirection;
    }

    /**
     * @param int$code
     * @param array $additionalData
     * @return array
     * @throws \RuntimeException
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    protected static function assembleFromCode($code, array $additionalData = array())
    {
        switch ($code) {
            case ClientRedirection::CODE_CONFIRM_MERGE_REQUEST:
                if (empty($additionalData['serviceId'])) {
                    throw new InvalidArgumentException('ClientRedirectionFactory::assembleFromCode() index serviceId in additionalData is missing (CODE_CONFIRM_MERGE_REQUEST).');
                }
                $urlPattern = ServiceModule::retrieveUri($additionalData['serviceId'], ServiceModule::MODULE_ACCOUNT_MERGING, $additionalData);
                if (empty($urlPattern)) {
                    throw new RuntimeException(sprintf("ClientRedirectionFactory::assembleFromCode() MODULE_ACCOUNT_MERGING not enabled for service with id %s or does not have uri setting defined (CODE_CONFIRM_MERGE_REQUEST).", $additionalData['serviceId']));
                }
                if (0 === strpos($urlPattern, '/')) {
                    $service = Service::find($additionalData['serviceId']);
                    if (empty($service->url)) {
                        throw new RuntimeException(sprintf("ClientRedirectionFactory::assembleFromCode() Service with id %s does not found or not have url defined (CODE_CONFIRM_MERGE_REQUEST).", $additionalData['serviceId']));
                    }
                    $baseUrl = rtrim($service->url, '/');
                    $urlPattern = $baseUrl.$urlPattern;
                }
                $result = ['urlPattern' => $urlPattern, 'remember' => true];
                break;
            case ClientRedirection::CODE_CONFIRM_MERGE_RESPONSE:
                if (empty($additionalData['url'])) {
                    throw new InvalidArgumentException('ClientRedirectionFactory::assembleFromCode() index serviceId in additionalData is missing (CODE_CONFIRM_MERGE_RESPONSE).');
                }
                $url = $additionalData['url'];
                $url = empty($additionalData['query']) ? $url : static::addQueryToUrl($url, $additionalData['query']);
                $result = ['urlSimple' => $url, 'remember' => false];
                break;
            default:
                throw new InvalidArgumentException(sprintf("ClientRedirectionFactory::assembleFromCode() unrecognized code '%s'", $code));
        }
        $result['code'] = $code;
        return $result;
    }

    /**
     * @param string $url
     * @param array $query
     * @return string
     */
    protected static function addQueryToUrl($url, array $query)
    {
        $parsed = parse_url($url);
        $originalQuery = Str::parseUrlQuery(Arr::get($parsed, 'query', ''));
        $mergedQuery = Arr::mergeNatural($originalQuery, $query);
        $parsed['query'] = http_build_query($mergedQuery);
        $result = Str::buildUrl($parsed);
        return $result;
    }
}
