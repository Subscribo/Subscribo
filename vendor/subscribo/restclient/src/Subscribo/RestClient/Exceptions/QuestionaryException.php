<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException;

class QuestionaryException extends ServerRequestException
{
    const STATUS_CODE = QuestionaryServerRequestHttpException::STATUS_CODE;
    const DEFAULT_MESSAGE = QuestionaryServerRequestHttpException::DEFAULT_MESSAGE;
    const DEFAULT_EXCEPTION_CODE = QuestionaryServerRequestHttpException::DEFAULT_EXCEPTION_CODE;

    public static function getKey()
    {
        return QuestionaryServerRequestHttpException::getKey();
    }
}
