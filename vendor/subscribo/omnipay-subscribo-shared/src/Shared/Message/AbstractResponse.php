<?php namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Common\Message\AbstractResponse as Base;
use Subscribo\Omnipay\Shared\Interfaces\ResponseCanHaveWidgetInterface;
use Subscribo\Omnipay\Shared\Interfaces\ResponseCanBeTransactionTokenInterface;
use Subscribo\Omnipay\Shared\Interfaces\ResponseCanBeWaitingInterface;

/**
 * Abstract class AbstractResponse
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractResponse extends Base implements
    ResponseCanHaveWidgetInterface,
    ResponseCanBeTransactionTokenInterface,
    ResponseCanBeWaitingInterface
{
    public function isTransactionToken()
    {
        return false;
    }

    public function isWaiting()
    {
        return false;
    }

    public function haveWidget()
    {
        return false;
    }

    public function getWidget()
    {
        return null;
    }

    public function getTransactionToken()
    {
        return null;
    }
}
