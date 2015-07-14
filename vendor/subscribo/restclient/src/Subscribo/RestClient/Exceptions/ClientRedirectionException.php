<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException;

/**
 * Class ClientRedirectionException
 * @package Subscribo\RestClient
 */
class ClientRedirectionException extends ServerRequestException
{
    const STATUS_CODE = ClientRedirectionServerRequestHttpException::STATUS_CODE;
    const DEFAULT_MESSAGE = ClientRedirectionServerRequestHttpException::DEFAULT_MESSAGE;
    const DEFAULT_EXCEPTION_CODE = ClientRedirectionServerRequestHttpException::DEFAULT_EXCEPTION_CODE;

    public static function getKey()
    {
        return ClientRedirectionServerRequestHttpException::getKey();
    }
}
