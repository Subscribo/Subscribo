<?php namespace Subscribo\RestCommon\Exceptions;

use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestCommon\Questionary;

class QuestionaryServerRequestHttpException extends ServerRequestHttpException
{
    const STATUS_CODE = 351;
    const DEFAULT_MESSAGE = 'Server asks questions';
    const DEFAULT_EXCEPTION_CODE = 51;

    protected static $serverRequestClassName = 'Subscribo\\RestCommon\\Questionary';
    protected static $keyName = Questionary::TYPE;

}
