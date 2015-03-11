<?php namespace Subscribo\OAuthCommon\Traits;

use Subscribo\OAuthCommon\Exceptions\ErrorResponseException;

trait OneResponseErrorsTrait
{
    public function user()
    {
        $this->throwExceptionOnErrorResponse();
        return parent::user();
    }



    protected function throwExceptionOnErrorResponse()
    {
        $denied = $this->request->query('denied');
        if ($denied) {
            throw new ErrorResponseException('Denied', 30);
        }
    }
}
