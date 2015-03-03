<?php namespace Subscribo\RestCommon\Exceptions;


use Exception;
use Subscribo\Exception\Exceptions\RedirectionHttpException;
use Subscribo\Exception\Interfaces\HttpExceptionInterface;
use Subscribo\Exception\Traits\StatusMessageTrait;
use Subscribo\RestCommon\Interfaces\ServerRequestInterface;

class ServerRequestHttpException extends RedirectionHttpException implements HttpExceptionInterface
{
    use StatusMessageTrait;
    const STATUS_CODE = 300;
    const DEFAULT_MESSAGE = 'Server Request';
    const DEFAULT_EXCEPTION_CODE = 0;

    protected static $serverRequestClassName = 'Subscribo\\RestCommon\\ServerRequest';
    protected static $keyName = 'serverRequest';

    /**
     * @var \Subscribo\RestCommon\Interfaces\ServerRequestInterface|array
     */
    protected $serverRequest;


    /**
     * @param ServerRequestInterface|array|mixed $serverRequest
     * @param bool|string $message
     * @param array $data
     * @param bool|int $code
     * @param Exception|null $previous
     * @param array $headers
     */
    public function __construct($serverRequest, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true ===  $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        $this->setServerRequest($serverRequest);
        $keyName = $this->getKey();
        foreach($this->getServerRequest()->export() as $key => $value) {
            $data[$keyName]['data'][$key] = $value;
        }

        $data[$keyName]['type'] = isset($data[$keyName]['type']) ? $data[$keyName]['type'] : $this->getServerRequest()->getType();
        $this->setStatusMessage($message);
        parent::__construct($this::STATUS_CODE, $message, $data, $code, $previous, $headers);
    }

    public static function getKey()
    {
        return static::$keyName;
    }

    public static function getServerRequestClassName()
    {
        return static::$serverRequestClassName;
    }

    public function getServerRequest()
    {
        return $this->serverRequest;
    }

    protected function setServerRequest($serverRequest)
    {
        if ($serverRequest instanceof ServerRequestInterface) {
            $this->serverRequest = $serverRequest;
            return $this;
        }
        $serverRequestClassName = $this->getServerRequestClassName();
        /** @var ServerRequestInterface $serverRequestInstance */
        $serverRequestInstance = new $serverRequestClassName($serverRequest);
        $this->serverRequest = $serverRequestInstance;
        return $this;
    }



}
