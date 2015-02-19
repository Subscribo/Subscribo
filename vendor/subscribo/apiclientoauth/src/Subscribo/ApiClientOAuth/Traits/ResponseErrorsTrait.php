<?php namespace Subscribo\ApiClientOAuth\Traits;

use Subscribo\ApiClientOAuth\Exceptions\EmptyCodeException;
use Subscribo\ApiClientOAuth\Exceptions\ErrorResponseException;

/**
 * Class ResponseErrorsTrait
 * To be used for classes extending \Laravel\Socialite\Two\AbstractProvider
 *
 * @package Subscribo\ApiClientOAuth
 */
trait ResponseErrorsTrait
{
    public function user()
    {
        $this->throwExceptionOnErrorResponse();
        $this->throwExceptionOnEmptyCode();
        return parent::user();
    }

    protected function throwExceptionOnErrorResponse()
    {
        $error = $this->request->query('error');
        $errorReason = $this->request->query('error_reason');
        $errorDescription = $this->request->query('error_description');
        if ($error or $errorReason or $errorDescription) {
            $message = sprintf('%s(%s): %s', $error, $errorReason, $errorDescription);
            throw new ErrorResponseException($message, 20);
        }
    }

    protected function throwExceptionOnEmptyCode()
    {
        $code = $this->getCode();
        if (empty($code)) {
            throw new EmptyCodeException('code empty', 10);
        }
    }
}
