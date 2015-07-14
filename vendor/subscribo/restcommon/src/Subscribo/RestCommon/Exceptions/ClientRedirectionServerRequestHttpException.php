<?php namespace Subscribo\RestCommon\Exceptions;

use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestCommon\ClientRedirection;

class ClientRedirectionServerRequestHttpException extends ServerRequestHttpException
{
    const STATUS_CODE = 352;
    const DEFAULT_MESSAGE = 'Server suggests client to redirect';
    const DEFAULT_EXCEPTION_CODE = 52;

    protected static $serverRequestClassName = 'Subscribo\\RestCommon\\ClientRedirection';
    protected static $keyName = ClientRedirection::TYPE;

}
