<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\RestClient\Traits\HttpExceptionConstructorTrait;
use Subscribo\RestClient\Exceptions\InvalidResponseException;

/**
 * Class InvalidRemoteServerResponseHttpException
 *
 * @package Subscribo\RestClient
 */
class InvalidRemoteServerResponseHttpException extends ServerErrorHttpException
{
    use HttpExceptionConstructorTrait;

    const DEFAULT_STATUS_CODE = 502;
    const DEFAULT_EXCEPTION_CODE = InvalidResponseException::DEFAULT_EXCEPTION_CODE;
    const DEFAULT_MESSAGE = InvalidResponseException::DEFAULT_MESSAGE;

}
