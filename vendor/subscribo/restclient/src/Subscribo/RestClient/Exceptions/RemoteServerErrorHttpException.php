<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\RestClient\Traits\HttpExceptionConstructorTrait;

/**
 * Class RemoteServerErrorHttpException
 *
 * @package Subscribo\RestClient
 */
class RemoteServerErrorHttpException extends ServerErrorHttpException
{
    use HttpExceptionConstructorTrait;

    const DEFAULT_STATUS_CODE = 502;
    const DEFAULT_EXCEPTION_CODE = 80;
    const DEFAULT_MESSAGE = 'Remote Server Error';

}
