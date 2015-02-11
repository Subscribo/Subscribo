<?php namespace Subscribo\Exception\Exceptions;

/**
 * Class InvalidIdentifierHttpException
 *
 * Class for errors caused by invalid format of identifier, which is part of uri
 *
 * @package Subscribo\Exception
 */
class InvalidIdentifierHttpException extends ValidationErrorsHttpException
{
    const DEFAULT_EXCEPTION_CODE = 30;
    const DEFAULT_MESSAGE = 'Invalid identifier';
}
