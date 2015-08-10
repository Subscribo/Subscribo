<?php

namespace Subscribo\RestClient\Exceptions;

use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException;

/**
 * Class WidgetException
 * @package Subscribo\RestClient
 */
class WidgetException extends ServerRequestException
{
    const STATUS_CODE = WidgetServerRequestHttpException::STATUS_CODE;
    const DEFAULT_MESSAGE = WidgetServerRequestHttpException::DEFAULT_MESSAGE;
    const DEFAULT_EXCEPTION_CODE = WidgetServerRequestHttpException::DEFAULT_EXCEPTION_CODE;

    public static function getKey()
    {
        return WidgetServerRequestHttpException::getKey();
    }
}
