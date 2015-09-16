<?php namespace Subscribo\RestClient\Factories;

use Subscribo\RestClient\Exceptions\QuestionaryException;
use Subscribo\RestClient\Exceptions\ClientRedirectionException;
use Subscribo\RestClient\Exceptions\WidgetException;
use Subscribo\RestClient\Exceptions\InvalidArgumentException;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Factories\ServerRequestFactory;

class ServerRequestExceptionFactory
{
    protected static $statusCodeMap = [
        QuestionaryException::STATUS_CODE => 'Subscribo\\RestClient\\Exceptions\\QuestionaryException',
        ClientRedirectionException::STATUS_CODE => 'Subscribo\\RestClient\\Exceptions\\ClientRedirectionException',
        WidgetException::STATUS_CODE => 'Subscribo\\RestClient\\Exceptions\\WidgetException',
    ];

    /**
     * @param int|string $statusCode
     * @return bool
     */
    public static function isServerRequestResponse($statusCode)
    {
        if (empty($statusCode)) {
            return false;
        }
        $statusCode = intval($statusCode);
        if (empty(static::$statusCodeMap[$statusCode])) {
            return false;
        }
        return true;
    }

    /**
     * @param int|string $statusCode
     * @param array $data
     * @return ServerRequestException
     * @throws \Subscribo\RestClient\Exceptions\InvalidArgumentException
     */
    public static function make($statusCode, array $data)
    {
        if ( ! static::isServerRequestResponse($statusCode)) {
            throw new InvalidArgumentException(sprintf("Unrecognized status code '%s'", $statusCode));
        }
        $statusCode = intval($statusCode);
        /** @var ServerRequestException $exceptionClassName */
        $exceptionClassName = static::$statusCodeMap[$statusCode];
        $keyName = $exceptionClassName::getKey();
        if (empty($data[$keyName])) {
            throw new InvalidArgumentException(sprintf("'Data does not contain required key '%s' or key empty", $keyName));
        }
        if ( ! is_array($data[$keyName])) {
            throw new InvalidArgumentException(sprintf("Key '%s' is not an array", $keyName));
        }
        /** @var ServerRequest $serverRequest */
        $serverRequest = ServerRequestFactory::make($data[$keyName]);
        $exception = new $exceptionClassName($serverRequest);
        return $exception;
    }

}
