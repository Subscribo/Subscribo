<?php namespace Subscribo\Exception\Exceptions;

//English Translations Resource file for ApiExceptionHandler

return [
    'suggestions' => [
        'specific' => [
            400 => [
                InvalidInputHttpException::DEFAULT_EXCEPTION_CODE => 'Check validationErrors for error messages for your input.',
                InvalidIdentifierHttpException::DEFAULT_EXCEPTION_CODE => 'Check format of identifier provided as part of URL.',
                InvalidQueryHttpException::DEFAULT_EXCEPTION_CODE => 'Check validationErrors for error messages for query parameters provided as part of your URL.',
                SessionVariableNotFoundHttpException::DEFAULT_EXCEPTION_CODE => 'Your session have been lost or variable, which was expected to be part of your session has not been found. This might happen e.g. when Back button of your browser is pressed in some circumstances.'
            ],
            403 => [
                WrongServiceHttpException::DEFAULT_EXCEPTION_CODE => 'You are trying to access resources from different service.',
                WrongAccountHttpException::DEFAULT_EXCEPTION_CODE => 'You are trying to access account from different service.',
            ],
            404 => [
                InstanceNotFoundHttpException::DEFAULT_EXCEPTION_CODE => 'Check ID or Identifier of requested object.',
            ],
        ],
        'fallback' => [
            400 => 'Check validationErrors for error messages.',
            403 => 'Check your authorization token and which resources you are trying to access.',
            401 => 'Check authorization header field',
            404 => 'Check your URL.',
            405 => 'Http method you have chosen is not supported by this endpoint.',
            500 => 'Server error occurred.',
        ],
        'marked' => 'Please contact an administrator and provide following exception hash: %mark% together with current date and time as well as accessed url or try again later.',
    ],
];
