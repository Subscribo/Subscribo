<?php namespace Subscribo\Auth\Guards;

use Subscribo\RestCommon\Signature;

class SubscriboDigestGuard extends SubscriboGuard
{
    protected $enforcedSignatureType = Signature::TYPE_SUBSCRIBO_DIGEST;
}
