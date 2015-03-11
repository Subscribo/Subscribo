<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use RuntimeException;
use Subscribo\Exception\Interfaces\ContainDataInterface;
use Subscribo\Exception\Traits\ContainDataTrait;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;
use GuzzleHttp\Exception\ConnectException;

/**
 * Class ConnectionException
 *
 * @package Subscribo\RestClient
 */
class ConnectionException extends RuntimeException implements ContainDataInterface, MarkableExceptionInterface
{
    use ContainDataTrait;
    use MarkableExceptionTrait;

    const DEFAULT_EXCEPTION_CODE = 90;
    const DEFAULT_MESSAGE = 'Could not connect to remote server';

    protected $_containedData = array();

    public function __construct(Exception $previous = null, array $data = null, $message = true, $code = true)
    {
        if (is_null($data)) {
            $data = array();
        }
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        if ($previous instanceof ConnectException) {
            $data['connection']['error']['message'] = $previous->getMessage();
        }
        $this->_containedData = $data;
        parent::__construct($message, $code, $previous);
    }
}
