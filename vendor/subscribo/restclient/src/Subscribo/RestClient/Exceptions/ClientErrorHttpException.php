<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\Exception\Interfaces\HttpExceptionInterface;
use Subscribo\Exception\Traits\StatusMessageTrait;

/**
 * Class ClientErrorHttpException
 *
 * @package Subscribo\RestClient
 */
class ClientErrorHttpException extends \Subscribo\Exception\Exceptions\ClientErrorHttpException implements HttpExceptionInterface
{
    use StatusMessageTrait;
}
