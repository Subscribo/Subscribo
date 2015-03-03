<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\RestClient\Exceptions\TokenConfigurationException;
use Subscribo\RestClient\Traits\HttpExceptionConstructorTrait;

/**
 * Class TokenConfigurationHttpException
 *
 * @package Subscribo\RestClient
 */
class TokenConfigurationHttpException extends ServerErrorHttpException
{
    use HttpExceptionConstructorTrait;

    const DEFAULT_STATUS_CODE = 571;
    const DEFAULT_EXCEPTION_CODE = TokenConfigurationException::DEFAULT_EXCEPTION_CODE;
    const DEFAULT_MESSAGE = TokenConfigurationException::DEFAULT_MESSAGE;

}
