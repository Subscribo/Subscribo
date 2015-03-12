<?php namespace Subscribo\OAuthCommon\Traits;

use Subscribo\OAuthCommon\Exceptions\EmptyCodeException;
use Subscribo\OAuthCommon\Exceptions\ErrorResponseException;

/**
 * Class TwoResponseErrorsTrait
 * To be used for classes extending \Laravel\Socialite\Two\AbstractProvider
 *
 * @package Subscribo\OAuthCommon
 */
trait TwoResponseErrorsTrait
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
