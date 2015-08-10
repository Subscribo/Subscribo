<?php namespace Subscribo\RestCommon\Exceptions;

use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestCommon\Widget;

class WidgetServerRequestHttpException extends ServerRequestHttpException
{
    const STATUS_CODE = 353;
    const DEFAULT_MESSAGE = 'Server suggests client to display a widget';
    const DEFAULT_EXCEPTION_CODE = 53;

    protected static $serverRequestClassName = 'Subscribo\\RestCommon\\Widget';
    protected static $keyName = Widget::TYPE;
}
