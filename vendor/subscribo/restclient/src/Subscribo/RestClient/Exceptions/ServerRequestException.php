<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\RestClient\Exceptions\RedirectionException;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestCommon\Interfaces\ServerRequestInterface;

class ServerRequestException extends RedirectionException
{
    const STATUS_CODE = ServerRequestHttpException::STATUS_CODE;
    const DEFAULT_MESSAGE = ServerRequestHttpException::DEFAULT_MESSAGE;
    const DEFAULT_EXCEPTION_CODE = ServerRequestHttpException::DEFAULT_EXCEPTION_CODE;


    /**
     * @var \Subscribo\RestCommon\Interfaces\ServerRequestInterface
     */
    protected $serverRequest;

    public function __construct(ServerRequestInterface $serverRequest, $statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = null)
    {
        $this->serverRequest = $serverRequest;
        $keyName = $this->getKey();
        $data[$keyName]['data'] = $serverRequest->export();
        $data[$keyName]['type'] = $serverRequest->getType();
        if (true ===  $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        $this->setStatusMessage($message);

        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }


    public static function getKey()
    {
        return ServerRequestHttpException::getKey();
    }

    public function getServerRequest()
    {
        return $this->serverRequest;
    }
}
