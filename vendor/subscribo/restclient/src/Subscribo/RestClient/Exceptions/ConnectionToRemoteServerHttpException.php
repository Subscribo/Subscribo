<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\RestClient\Traits\HttpExceptionConstructorTrait;
use Subscribo\RestClient\Exceptions\ConnectionException;

/**
 * Class ConnectionToRemoteServerHttpException
 *
 * @package Subscribo\RestClient
 */
class ConnectionToRemoteServerHttpException extends ServerErrorHttpException
{
    use HttpExceptionConstructorTrait;

    const DEFAULT_STATUS_CODE = 502;
    const DEFAULT_EXCEPTION_CODE = ConnectionException::DEFAULT_EXCEPTION_CODE;
    const DEFAULT_MESSAGE = ConnectionException::DEFAULT_MESSAGE;

}
