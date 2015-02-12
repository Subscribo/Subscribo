<?php namespace Subscribo\RestClient\Exceptions;


class InvalidRemoteServerResponseHttpException extends RemoteServerErrorHttpException
{
    const DEFAULT_EXCEPTION_CODE = 90;
    const DEFAULT_MESSAGE = 'Invalid response from remote server';
}
