<?php namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Common\Message\AbstractResponse as Base;

abstract class AbstractResponse extends Base
{
    public function isTransactionToken()
    {
        return false;
    }

    public function haveWidget()
    {
        return false;
    }

}
