<?php namespace Omnipay\PayUnity;

use Subscribo\Omnipay\Shared\AbstractGateway as Base;

/**
 * Abstract class AbstractGateway
 *
 * @package Omnipay\PayUnity
 */
abstract class AbstractGateway extends Base
{
    public function getName()
    {
        return 'PayUnity';
    }

}
