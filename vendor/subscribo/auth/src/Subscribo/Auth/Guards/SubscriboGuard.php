<?php namespace Subscribo\Auth\Guards;

use Subscribo\Auth\Guards\BaseStatelessGuard;
use Subscribo\RestCommon\Interfaces\ByTokenIdentifiableFactoryInterface;
use Subscribo\Auth\Interfaces\StatelessAuthenticatableFactoryInterface;
use Subscribo\Auth\Interfaces\ApiGuardInterface;
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
class SubscriboGuard extends BaseStatelessGuard implements GuardContract, ApiGuardInterface
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

    /**
     * @var array|null|bool
     */
    protected $processingResult = false;

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
        $this->processRequest($this->request);

        return $this->user;
    }

    /**
     * @param Request $request
     * @return Request|null
     */
    public function processRequest(Request $request)
    {
        $this->request = $request;
        $encrypter = ($this->commonSecretProvider) ? $this->commonSecretProvider->getCommonSecretEncrypter() : null;
        $this->processingResult = Signature::processIncomingRequest(
            $this->request,
            [$this->userWithTokenFactory, 'tokenToTokenRingProvider'],
            $encrypter,
            $this->enforcedSignatureType
        );
        if (empty($this->processingResult)) {
            $this->user = null;
            $this->processingResult = null;
            return null;
        }
        $this->user = $this->userWithTokenFactory->findByTokenIdentifiableUsingTokenRingProvider($this->processingResult['tokenRingProvider']);
        if ($this->user) {
            $this->loggedOut = false;
        }
        return $this->processingResult['processedRequest'];
    }

    /**
     * @return array|null
     */
    public function processingResult()
    {
        if (false === $this->processingResult) {
            $this->processRequest($this->request);
        }
        return $this->processingResult;
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
