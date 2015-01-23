<?php namespace Subscribo\RestCommon\Interfaces;

use Subscribo\RestCommon\TokenRing;
use Subscribo\RestCommon\Interfaces\TokenRingProviderInterface;
use Subscribo\RestCommon\Interfaces\ByTokenIdentifiableInterface;


interface ByTokenIdentifiableFactoryInterface
{
    /**
     * @param string $token
     * @param string|null $tokenType
     * @return TokenRingProviderInterface
     */
    public function tokenToTokenRingProvider($token, $tokenType = null);

    /**
     * @param TokenRingProviderInterface $tokenRingProvider
     * @return ByTokenIdentifiableInterface
     */
    public function findByTokenIdentifiableUsingTokenRingProvider(TokenRingProviderInterface $tokenRingProvider);

}
