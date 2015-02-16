<?php namespace Subscribo\Api1;

use Subscribo\Auth\Interfaces\ApiGuardInterface;
use Subscribo\App\Model\Account;
use Subscribo\App\Model\Service;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;
use Subscribo\RestCommon\AccountIdTransport;

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
     * @var null|bool|\Subscribo\App\Model\User
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
     * @var bool|null|int
     */
    protected $accountId = false;


    /**
     * @param ApiGuardInterface $auth
     */
    public function __construct(ApiGuardInterface $auth)
    {
        $this->auth = $auth;
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
     * @return null|\Subscribo\App\Model\User
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
     * @return null|\Subscribo\App\Model\User
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
     * @return int
     */
    public function getAccountId()
    {
        if (false === $this->accountId)
        {
            return $this->retrieveAccountId();
        }
        return $this->accountId;
    }

    /**
     * @return int|null
     */
    public function retrieveAccountId()
    {
        $this->account = false;

        $this->accountId = $this->retrieveAccountIdFromRequestQuery()
            ?: AccountIdTransport::extractAccountIdFromProcessIncomingRequestResult($this->auth->processingResult());

        return $this->accountId;
    }

    protected function retrieveAccountIdFromRequestQuery()
    {
        $request = $this->getRequest();
        $accountIdInQuery = $request->query('account_id', null);
        if (empty($accountIdInQuery)) {
            return null;
        }
        return intval($accountIdInQuery);
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
        $accountId = $this->retrieveAccountId();
        if (empty($accountId)) {
            $this->account = null;
            return null;
        }
        $this->account = Account::find($accountId);
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
}
