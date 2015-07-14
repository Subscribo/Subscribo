<?php namespace Subscribo\RestClient\Traits;

use Exception;
use Subscribo\Exception\Interfaces\ContainDataInterface;

/**
 * Class HttpExceptionConstructorTrait
 *
 * Class implementing this interface should define following constants: DEFAULT_STATUS_CODE, DEFAULT_EXCEPTION_CODE, DEFAULT_MESSAGE
 *
 * @package Subscribo\RestClient
 */
trait HttpExceptionConstructorTrait
{
    public function __construct(Exception $previous = null, array $data = null, $statusCode = true, $message = true, $code = true, array $headers = array())
    {
        if (is_null($data)) {
            $data = ($previous instanceof ContainDataInterface) ? $previous->getData() : array();
        }
        if (true === $statusCode) {
            $statusCode = $this::DEFAULT_STATUS_CODE;
        }
        if (true === $code) {
            $code = $previous ? $previous->getCode() : $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $previous ? $previous->getMessage() : $this::DEFAULT_MESSAGE;
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
