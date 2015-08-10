<?php

namespace Subscribo\RestCommon\Factories;

use Exception;
use Subscribo\RestCommon\Interfaces\ServerRequestInterface;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestCommon\Widget;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;

/**
 * Class ServerRequestHttpExceptionFactory
 *
 * @package Subscribo\RestCommon
 */
class ServerRequestHttpExceptionFactory
{
    protected static $typeMap = [
        Questionary::TYPE => 'Subscribo\\RestCommon\\Exceptions\\QuestionaryServerRequestHttpException',
        ClientRedirection::TYPE => 'Subscribo\\RestCommon\\Exceptions\\ClientRedirectionServerRequestHttpException',
        Widget::TYPE => 'Subscribo\\RestCommon\\Exceptions\\WidgetServerRequestHttpException',
    ];

    /**
     * @param ServerRequestInterface $serverRequest
     * @param bool|string $message
     * @param array $data
     * @param bool|int $code
     * @param Exception|null $previous
     * @param array $headers
     * @return ServerRequestHttpException|\Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException|\Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException|\Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     */
    public static function make(ServerRequestInterface $serverRequest, $message = true, array $data = [], $code = true, Exception $previous = null, array $headers = [])
    {
        $type = $serverRequest->getType();
        if (empty(static::$typeMap[$type])) {

            return new ServerRequestHttpException($serverRequest, $message, $data, $code, $previous, $headers);
        }
        $className = static::$typeMap[$type];

        return new $className($serverRequest, $message, $data, $code, $previous, $headers);
    }
}
