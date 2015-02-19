<?php namespace Subscribo\Auth\Guards;

use Subscribo\RestCommon\Signature;

class SubscriboBasicGuard extends SubscriboGuard
{
    protected $enforcedSignatureType = Signature::TYPE_SUBSCRIBO_BASIC;
}
