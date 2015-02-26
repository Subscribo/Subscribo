<?php namespace Subscribo\RestCommon\Factories;

use Subscribo\RestCommon\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException;
use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\Factories\ServerRequestFactory;

class ServerRequestHttpExceptionFactory
{
    protected static $statusCodeMap = [
        QuestionaryServerRequestHttpException::STATUS_CODE => 'Subscribo\\RestCommon\\Exceptions\\QuestionaryServerRequestHttpException',

    ];

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
     * @param int $statusCode
     * @param array $data
     * @return ServerRequestHttpException
     * @throws \Subscribo\RestCommon\Exceptions\InvalidArgumentException
     */
    public static function make($statusCode, array $data)
    {
        if ( ! static::isServerRequestResponse($statusCode)) {
            throw new InvalidArgumentException(sprintf("Unrecognized status code '%s'", $statusCode));
        }
        $statusCode = intval($statusCode);
        /** @var ServerRequestHttpException $exceptionClassName */
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
