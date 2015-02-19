<?php namespace Subscribo\Api1\Controllers;

use LogicException;
use Subscribo\Api1\AbstractController;
use Subscribo\App\Model\AccountToken;
use Subscribo\App\Model\Customer;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;
use Subscribo\App\Model\ServicePool;
use Subscribo\App\Model\Account;


class AccountController extends AbstractController
{

    private $commonValidationRules = [
        'email' => 'required|email|max:255',
        'password' => 'required|min:5',
    ];

    public function actionPostRegistration()
    {
        $serviceId = $this->context->getServiceId();
        $rules = [
            'name'  => 'max:255',
            'email' => 'required_without:oauth|email|max:255',
            'password' => 'required_without:oauth|min:5',
            'oauth' => 'array',
        ];
        $validated = $this->validateRequestBody($rules);
        if ( ! empty($validated['oauth'])) {
            return $this->oAuthRegistration($validated, $serviceId);
        }
        $emailUsed = $this->checkEmailUsed($validated['email'], $serviceId);
        if (false === $emailUsed) {
            return $this->performRegistration($validated, $serviceId);
        }
        if (true === $emailUsed) {
            throw new InvalidInputHttpException(['email' => 'Email already used for this service']);
        }
        if (is_array($emailUsed)) {
            return $emailUsed;
        }
        throw new LogicException('checkEmailUsed should return bool or array');
    }

    /**
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    public function actionGetValidation()
    {
        $validated = $this->validateRequestQuery($this->commonValidationRules);
        return $this->processValidation($validated, 'GET');
    }

    /**
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    public function actionPostValidation()
    {
        $validated = $this->validateRequestBody($this->commonValidationRules);
        return $this->processValidation($validated, 'POST');
    }


    /**
     * @param $validated
     * @param string $method
     * @return array
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException|\Subscribo\Exception\Exceptions\InvalidQueryHttpException
     */
    public function processValidation($validated, $method)
    {
        /** @var \Subscribo\App\Model\Factories\CustomerFactory $customerFactory */
        $customerFactory = $this->applicationMake('Subscribo\\App\\Model\\Factories\\CustomerFactory');
        $found = $customerFactory->find($this->context->getServiceId(), $validated);
        if (empty($found)) {
            return ['validated' => false];
        }
        if ($customerFactory->checkCustomerPassword($found['customer'], $validated['password'])) {
            return ['validated' => true, 'result' => $found];
        }
        return ['validated' => false];
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
    private function retrieveAccount($accountId)
    {
        $accountId = is_null($accountId) ? $this->context->getAccountId() : $this->validatePositiveIdentifier($accountId);
        $account = Account::find($accountId);
        if (empty($account)) {
            throw new InstanceNotFoundHttpException();
        }
        $this->context->checkServiceForAccount($account);
        return $account;
    }

    private function assembleAccountResult(Account $account)
    {
        $person = $account->customer ? $account->customer->person : null;
        $result = [
            'account' => $account,
            'customer' => $account->customer,
            'person' => $person,
        ];
        return $result;
    }

    /**
     * @param array $data
     * @param int $serviceId
     * @return array
     */
    private function performRegistration(array $data, $serviceId)
    {
        /** @var \Subscribo\App\Model\Factories\CustomerFactory $customerFactory */
        $customerFactory = $this->applicationMake('Subscribo\\App\\Model\\Factories\\CustomerFactory');

        $registered = $customerFactory->register($serviceId, $data);

        return  ['registered' => true, 'result' => $registered ];
    }

    /**
     * @param $method
     * @return InvalidInputHttpException|InvalidQueryHttpException
     */
    private function assembleCredentialsNotValidException($method)
    {
        $errors = [
            'email' => 'Credentials not valid for this service',
            'password' => 'Credentials not valid for this service',
        ];
        if ('GET' === $method) {
            return new InvalidQueryHttpException($errors);
        }
        return new InvalidInputHttpException($errors);
    }

    private function oAuthRegistration(array $data, $serviceId)
    {
        $validatedOAuthData = $this->validateOAuthData($data);
        $alreadyRegistered = AccountToken::findByIdentifierAndServiceId($validatedOAuthData['identifier'], $serviceId);
        if ($alreadyRegistered) {
            return ['registered' => true, 'result' => $this->assembleAccountResult($alreadyRegistered->account)];
        }
        if (empty($data['email']) or ( ! $this->isEmailAcceptable($data['email']))) {
            return $this->assembleAskForEmail();
        }
        $emailUsed = $this->checkEmailUsed($data['email'], $serviceId);
        if (false === $emailUsed) {
            return $this->performRegistration($data, $serviceId);
        }
        if (true === $emailUsed) {
            return $this->assembleAskForEmailOrPassword();
        }
        if (is_array($emailUsed)) {
            return $emailUsed;
        }
        throw new LogicException('checkEmailUsed should be bool or array');
    }

    /**
     * @param array $data
     * @return array
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function validateOAuthData(array $data)
    {
        if (empty($data['oauth']) or ( ! is_array($data['oauth']))) {
            throw new InvalidInputHttpException(['oauth' => 'OAuth data empty']);
        }
        $oAuthData = $data['oauth'];
        $rules = [
            'identifier'    => 'required|max:255',
            'provider'      => 'required|in:facebook',
            'token'         => 'max:255',
        ];
        $validator = $this->assembleValidator($oAuthData, $rules);
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->errors()->all());
        }
        return $validator->valid();
    }


    /**
     * @param string $email
     * @param int $serviceId
     * @return array|bool
     */
    private function checkEmailUsed($email, $serviceId)
    {
        $alreadyRegisteredCustomers = Customer::findAllByEmail($email);

        if (empty($alreadyRegisteredCustomers)) {
            return false;
        }
        $service = $this->context->getService();
        $servicePools = $service->servicePools;

        $compatibleServices = [];

        foreach ($alreadyRegisteredCustomers as $customer) {
            foreach ($customer->accounts as $account) {
                $serviceIdToCheck = intval($account->serviceId);
                if ($serviceId === $serviceIdToCheck) {
                    return true;
                }
                if (ServicePool::isInPool($servicePools, $serviceIdToCheck)) {
                    $compatibleServices[] = $serviceIdToCheck;
                }
            }
        }
        if (empty($compatibleServices)) {
            return false;
        }
        return [
            'asking' => true,
            'questions' => [[
                'type' => 'select',
                'text' => 'Would you like to merge your account with existing account?',
                'code' => 10,
                'options' => $compatibleServices,
            ]]
        ];

    }

    private function assembleAskForEmail()
    {
        return [
            'asking' => true,
            'questions' => [[
                'type' => 'email',
                'text' => 'Your email:',
                'code' => 20,
            ]],
        ];
    }

    private function assembleAskForEmailOrPassword()
    {
        return [
            'asking' => true,
            'title' => 'Would you like to login to your current account or provide a new one?',
            'questions' => [
                [
                    'type' => 'email',
                    'text' => 'Your email:',
                    'code' => 30,
                ],
                [
                    'type' => 'password',
                    'text' => 'Password to your current account',
                    'code' => 40,
                ]
            ],
        ];
    }

    private function isEmailAcceptable($email)
    {
        return true;

    }

}
