<?php

namespace Subscribo\Api1;

use Subscribo\Auth\Interfaces\ApiGuardInterface;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Service;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;
use Subscribo\RestCommon\AccountAccessTokenTransport;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class Context
 * Context for Api v1 controllers (giving information about request, logged in user, etc.)
 *
 * @package Subscribo\Api1
 */
class Context
{
    /**
     * @var \Subscribo\Auth\Interfaces\ApiGuardInterface
     */
    protected $auth;

    /**
     * @var null|bool|\Subscribo\ModelCore\Models\User
     */
    protected $user = false;

    /**
     * @var bool|null|Service
     */
    protected $service = false;

    /**
     * @var bool|null|int
     */
    protected $serviceId = false;

    /**
     * @var bool|null|Account
     */
    protected $account = false;

    /**
     * @var bool|string
     */
    protected $accountAccessToken = false;

    /**
     * @var LocalizerInterface
     */
    protected $localizer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ApiGuardInterface $auth
     * @param LocalizerInterface $localizer
     * @param LoggerInterface $logger
     */
    public function __construct(ApiGuardInterface $auth, LocalizerInterface $localizer, LoggerInterface $logger)
    {
        $this->auth = $auth;
        $this->localizer = $localizer;
        $this->logger = $logger;
    }

    /**
     * @return null|\Illuminate\Http\Request
     */
    public function getRequest()
    {
        $processingResult = $this->auth->processingResult();
        if (empty($processingResult['processedRequest'])) {
            return null;
        }
        return $processingResult['processedRequest'];
    }

    /**
     * @return null|\Subscribo\ModelCore\Models\User
     */
    public function getUser()
    {
        if (false === $this->user)
        {
            return $this->retrieveUser();
        }
        return $this->user;
    }


    /**
     * @return null|\Subscribo\ModelCore\Models\User
     */
    public function retrieveUser()
    {
        $this->user = $this->auth->user();
        return $this->user;
    }

    /**
     * @return int|null
     */
    public function getServiceId()
    {
        if (false === $this->serviceId) {
            return $this->retrieveServiceId();
        }
        return $this->serviceId;
    }

    /**
     * @return int|null
     */
    public function retrieveServiceId()
    {
        $this->service = false;
        $user = $this->retrieveUser();
        if ($user) {
            $this->serviceId = $user->serviceId;
        } else {
            $this->serviceId = null;
        }
        return $this->serviceId;
    }

    /**
     * @return null|Service
     */
    public function getService()
    {
        if (false === $this->service) {
            return $this->retrieveService();
        }
        return $this->service;
    }

    /**
     * @return null|Service
     */
    public function retrieveService()
    {
        $serviceId = $this->retrieveServiceId();
        if (empty($serviceId)) {
            $this->service = null;
            return null;
        }
        $this->service = Service::find($serviceId);
        return $this->service;
    }

    /**
     * @return string|null
     */
    public function getAccountAccessToken()
    {
        if (false === $this->accountAccessToken) {

            return $this->retrieveAccountAccessToken();
        }

        return $this->accountAccessToken;
    }

    public function retrieveAccountAccessToken()
    {
        $this->account = false;

        $this->accountAccessToken = $this->retrieveAccountAccessTokenFromRequestQuery()
            ?: AccountAccessTokenTransport::extractAccountAccessTokenFromProcessIncomingRequestResult(
                $this->auth->processingResult()
            );

        return $this->accountAccessToken;
    }

    /**
     * @deprecated
     * @return int
     */
    public function getAccountId()
    {
        $this->account = $this->getAccount();

        if ($this->account) {

            return intval($this->account->id);
        }

        return null;
    }

    /**
     * @deprecated
     * @return int|null
     */
    public function retrieveAccountId()
    {
        $this->account = $this->retrieveAccount();

        if ($this->account) {

            return intval($this->account->id);
        }

        return null;
    }

    /**
     * @param bool $autoCheck
     * @return null|Account
     */
    public function getAccount($autoCheck = true)
    {
        if (false === $this->account) {

            return $this->retrieveAccount($autoCheck);
        }
        if ($autoCheck) {
            $this->checkServiceForAccount($this->account);
        }

        return $this->account;
    }

    /**
     * @param bool $autoCheck
     * @return null|Account
     */
    public function retrieveAccount($autoCheck = true)
    {
        $accountAccessToken = $this->retrieveAccountAccessToken();
        if (empty($accountAccessToken)) {
            $this->account = null;

            return null;
        }
        $this->account = Account::findByAccountAccessToken($accountAccessToken);
        if ($autoCheck) {
            $this->checkServiceForAccount($this->account);
        }

        return $this->account;
    }

    /**
     * @param Account|null $account
     * @throws \Subscribo\Exception\Exceptions\WrongServiceHttpException
     */
    public function checkServiceForAccount(Account $account = null)
    {
        if (is_null($account)) {

            return;
        }
        if ($account->serviceId !== $this->getServiceId()) {
            throw new WrongServiceHttpException();
        }
    }

    public function getLocalizer()
    {
        return $this->localizer;
    }

    public function getLocale()
    {
        return $this->localizer->getLocale();
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param string $message
     * @param array $context
     * @param string $level
     */
    public function log($message, array $context = [], $level = LogLevel::NOTICE)
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * @return string|null
     */
    protected function retrieveAccountAccessTokenFromRequestQuery()
    {
        return $this->getRequest()->query('account_access_token', null);
    }
}
