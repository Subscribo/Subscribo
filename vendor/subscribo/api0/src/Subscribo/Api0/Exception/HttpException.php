<?php namespace Subscribo\Api0\Exception;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;


/**
 * Class HttpException Base Http Exception class for API v0
 *
 * @package Subscribo\Api0
 */
class HttpException extends \Symfony\Component\HttpKernel\Exception\HttpException {

    protected $_contentData = array();

    /**
     * @param int $statusCode
     * @param string|null $message
     * @param array $data
     * @param \Exception $previous
     * @param array $headers
     * @param int $code
     */
    public function __construct($statusCode, $message = null, $data = array(), \Exception $previous = null, $headers = array(), $code = 0)
    {
        if (isset($data['message'])) {
            $message = $data['message'];
        }
        if (is_null($message)) {
            if (0 === $code) {
                $translationKey = 'api0::ApiV0HttpExceptionMessages.default.' . $statusCode;
            } else {
                $translationKey = 'api0::ApiV0HttpExceptionMessages.special.' . $statusCode . '.' . $code;
            }
            if (Lang::has($translationKey)) {
                $message = Lang::get($translationKey);
            } else {
                $message = 'HTTP ERROR WITHOUT SPECIFIED MESSAGE';
            }
        }
        if ( ! isset($data['exception_code'])) {
            $data = (array('exception_code' => $code) + $data);
        }
        if ( ! isset($data['http_status_code'])) {
            $data = (array('http_status_code' => $statusCode) + $data);
        }
        if ( ! isset($data['message'])) {
            $data = (array('message' => $message) + $data);
        }

        $this->_contentData = $data;

        parent::__construct($statusCode, $message, $previous, $headers, $code);

    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function forgeResponse(Request $request)
    {
        $response = Response::create($this->_contentData, $this->getStatusCode(), $this->getHeaders());
        return $response;
    }
}

