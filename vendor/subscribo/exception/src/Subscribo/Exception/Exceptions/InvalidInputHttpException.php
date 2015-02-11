<?php namespace Subscribo\Exception\Exceptions;

/**
 * Class InvalidInputHttpException
 *
 * Class for Input Validation errors
 *
 * @package Subscribo\Exception
 */
class InvalidInputHttpException extends ValidationErrorsHttpException
{

    const DEFAULT_EXCEPTION_CODE = 10;
    const DEFAULT_MESSAGE = 'Invalid input';

}
