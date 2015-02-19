<?php namespace Subscribo\RestCommon\Interfaces;

use Subscribo\RestCommon\TokenRing;
use Subscribo\RestCommon\Interfaces\ByTokenIdentifiableInterface;

interface TokenRingProviderInterface
{

    /**
     * @return TokenRing|string|array|null;
     */
    public function provideTokenRing();

    /**
     * @return ByTokenIdentifiableInterface|null;
     */
    public function provideByTokenIdentifiable();

}