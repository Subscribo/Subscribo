<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use RuntimeException;
use Subscribo\Exception\Interfaces\ContainDataInterface;
use Subscribo\Exception\Traits\ContainDataTrait;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class InvalidResponseException
 *
 * @package Subscribo\RestClient
 */
class InvalidResponseException extends RuntimeException implements ContainDataInterface, MarkableExceptionInterface
{
    use ContainDataTrait;
    use MarkableExceptionTrait;

    const DEFAULT_EXCEPTION_CODE = 90;
    const DEFAULT_MESSAGE = 'Invalid response from remote server';

    protected $_containedData = array();

    public function __construct(array $data = null, $message = true, $code = true, Exception $previous = null)
    {
        if (is_null($data)) {
            $data = array();
        }
        if (true === $code) {
            $code = $previous ? $previous->getCode() : $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $previous ? $previous->getMessage() : $this::DEFAULT_MESSAGE;
        }
        $this->_containedData = $data;
        parent::__construct($message, $code, $previous);
    }
}
