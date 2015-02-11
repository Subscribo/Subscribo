<?php namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractController;
use Subscribo\App\Model\Customer;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;
use Subscribo\App\Model\ServicePool;
use Subscribo\App\Model\Account;


class AccountController extends AbstractController
{

    protected $commonValidationRules = [
        'email' => 'required|email',
        'password' => 'required|min:5',
    ];

    public function actionPostRegistration()
    {
        $rules = $this->commonValidationRules;
        $rules['name'] = '';
        $validated = $this->validateRequestBody($rules);
        $alreadyRegisteredCustomers = Customer::findAllByEmail($validated['email']);
        $serviceId = $this->context->getServiceId();

        if (empty($alreadyRegisteredCustomers)) {
            return $this->performRegistration($validated, $serviceId);
        }
        $service = $this->context->getService();
        $servicePools = $service->servicePools;

        $compatibleServices = [];

        foreach ($alreadyRegisteredCustomers as $customer) {
            foreach ($customer->accounts as $account) {
                $serviceIdToCheck = intval($account->serviceId);
                if ($serviceId === $serviceIdToCheck) {
                    throw new InvalidInputHttpException(['email' => 'Email already used for this service']);
                }
                if (ServicePool::isInPool($servicePools, $serviceIdToCheck)) {
                    $compatibleServices[] = $serviceIdToCheck;
                }
            }
        }
        if (empty($compatibleServices)) {
            return $this->performRegistration($validated, $serviceId);
        }
        return [
            'question' => [
                'type' => 'select',
                'text' => 'Would you like to merge your account with existing account?',
                'code' => 10,
                'options' => $compatibleServices,
            ]
        ];
    }

    /**
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    public function actionGetValidated()
    {
        $validated = $this->validateRequestQuery($this->commonValidationRules);
        return $this->processValidation($validated);
    }

    /**
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    public function actionPostValidated()
    {
        $validated = $this->validateRequestBody($this->commonValidationRules);
        return $this->processValidation($validated);
    }


    /**
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    public function processValidation($validated)
    {
        /** @var \Subscribo\App\Model\Factories\CustomerFactory $customerFactory */
        $customerFactory = $this->applicationMake('Subscribo\\App\\Model\\Factories\\CustomerFactory');
        $found = $customerFactory->find($this->context->getServiceId(), $validated);
        if (empty($found)) {
            throw $this->assembleCredentialsNotValidException();
        }
        if ($customerFactory->checkCustomerPassword($found['customer'], $validated['password'])) {
            return ['validated' => true, 'result' => $found];
        }
        throw $this->assembleCredentialsNotValidException();
    }

    public function actionGetRemembered($accountId = null)
    {
        $accountId = is_null($accountId) ? $this->context->getAccountId() : $this->validatePositiveIdentifier($accountId);
        $validated = $this->validateRequestQuery([
            'token' => 'required',
        ]);
        /** @var Account $account */
        $account = Account::findRemembered($validated['token'], $accountId, $this->context->getServiceId());
        if (empty($account)) {
            throw new InvalidQueryHttpException(['token' => 'Account with given ID and token not found for this service']);
        }

        return ['found' => true, 'result' => $this->assembleAccountResult($account)];
    }

    public function actionPutRemembered($accountId = null)
    {
        $validated = $this->validateRequestBody([
            'token' => 'required_without:forget',
            'forget' => 'boolean',
        ]);
        $account = $this->retrieveAccount($accountId);
        $account->rememberToken = $validated['token'] ?: null;
        $account->save();

        return ['remembered' => true, 'result' => $this->assembleAccountResult($account)];
    }


    public function actionGetDetail($accountId = null)
    {
        $account = $this->retrieveAccount($accountId);

        return ['found' => true, 'result' => $this->assembleAccountResult($account)];
    }

    /**
     * @param int|null $accountId
     * @return Account
     * @throws \Subscribo\Exception\Exceptions\InstanceNotFoundHttpException
     */
    protected function retrieveAccount($accountId)
    {
        $accountId = is_null($accountId) ? $this->context->getAccountId() : $this->validatePositiveIdentifier($accountId);
        $account = Account::find($accountId);
        if (empty($account)) {
            throw new InstanceNotFoundHttpException();
        }
        $this->context->checkServiceForAccount($account);
        return $account;
    }

    protected function assembleAccountResult(Account $account)
    {
        $person = $account->customer ? $account->customer->person : null;
        $result = [
            'account' => $account,
            'customer' => $account->customer,
            'person' => $person,
        ];
        return $result;
    }

    protected function performRegistration(array $data, $serviceId)
    {
        /** @var \Subscribo\App\Model\Factories\CustomerFactory $customerFactory */
        $customerFactory = $this->applicationMake('Subscribo\\App\\Model\\Factories\\CustomerFactory');

        $registered = $customerFactory->register($serviceId, $data);

        return  ['registered' => true, 'result' => $registered ];
    }

    /**
     * @return InvalidInputHttpException
     */
    protected function assembleCredentialsNotValidException()
    {
        return new InvalidQueryHttpException([
            'email' => 'Credentials not valid for this service',
            'password' => 'Credentials not valid for this service',
        ]);
    }

}
