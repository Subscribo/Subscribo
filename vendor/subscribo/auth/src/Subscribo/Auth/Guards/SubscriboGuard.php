<?php namespace Subscribo\Auth\Guards;

use Subscribo\Auth\Guards\BaseStatelessGuard;
use Subscribo\RestCommon\Interfaces\ByTokenIdentifiableFactoryInterface;
use Subscribo\Auth\Interfaces\StatelessAuthenticatableFactoryInterface;
use Subscribo\RestCommon\Signature;
use Subscribo\RestCommon\Interfaces\CommonSecretProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Auth\Guard as GuardContract;
use Subscribo\Auth\Traits\StatelessToNonStatelessTrait;
use Subscribo\RestCommon\Exceptions\UnauthorizedHttpException;

/**
 * Class SubscriboGuard
 *
 * @package Subscribo\Auth
 */
class SubscriboGuard extends BaseStatelessGuard implements GuardContract
{
    use StatelessToNonStatelessTrait;
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Subscribo\RestCommon\Interfaces\CommonSecretProviderInterface
     */
    protected $commonSecretProvider;

    /**
     * @var bool|string
     */
    protected $enforcedSignatureType = false;

    /**
     * @var \Subscribo\RestCommon\Interfaces\ByTokenIdentifiableFactoryInterface
     */
    protected $userWithTokenFactory;

    public function __construct(StatelessAuthenticatableFactoryInterface $userFactory, ByTokenIdentifiableFactoryInterface $userWithTokenFactory, Request $request, CommonSecretProviderInterface $commonSecretProvider)
    {
        $this->userWithTokenFactory = $userWithTokenFactory;
        $this->request = $request;
        $this->commonSecretProvider = $commonSecretProvider;

        parent::__construct($userFactory);
    }

    /**
     * Main authentication function
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|\Subscribo\RestCommon\Interfaces\ByTokenIdentifiableInterface|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }

        if ($this->user) {
            return $this->user;
        }

        if (empty($this->request)) {
            return null;
        }
        $encrypter = ($this->commonSecretProvider) ? $this->commonSecretProvider->getCommonSecretEncrypter() : null;
        $verificationResult = Signature::verifyRequest(
            $this->request,
            [$this->userWithTokenFactory, 'tokenToTokenRingProvider'],
            $encrypter,
            $this->enforcedSignatureType
        );
        if (empty($verificationResult)) {
            return null;
        }
        $this->user = $this->userWithTokenFactory->findByTokenIdentifiableUsingTokenRingProvider($verificationResult['tokenRingProvider']);

        return $this->user;
    }

    /**
     * This method is here to conform with GuardContract
     *
     * It however does not return Response, only null, if user is authenticated via token
     * and throws exception, which should be converted to 401 Response otherwise
     *
     * @param string $field
     * @return null
     * @throws \Subscribo\RestCommon\Exceptions\UnauthorizedHttpException
     */
    public function onceBasic($field = 'email')
    {
        if ($this->guest()) {
            throw new UnauthorizedHttpException();
        }
        return null;
    }
}
