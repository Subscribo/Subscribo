<?php namespace Subscribo\Api1\Controllers;

use RuntimeException;
use Subscribo\ModelCore\Models\AccountToken;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\CustomerRegistration;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\ModelCore\Models\ServicePool;
use Subscribo\ModelCore\Models\Account;
use Subscribo\OAuthCommon\AbstractOAuthManager;
use Subscribo\RestCommon\Questionary;
use Subscribo\Api1\AbstractController;
use Subscribo\Api1\Factories\AccountFactory;
use Subscribo\Api1\Factories\CustomerRegistrationFactory;
use Subscribo\Api1\Factories\QuestionaryFactory;
use Subscribo\Api1\Context;

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
        $emailUsed = $this->checkEmailUsed($validated['email'], $serviceId, $validated);
        if ($emailUsed) {
            throw new InvalidInputHttpException(['email' => 'Email already used for this service']);
        }
        return $this->performRegistration($validated, $serviceId);
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
    private function processValidation($validated, $method)
    {
        /** @var \Subscribo\Api1\Factories\AccountFactory $accountFactory */
        $accountFactory = $this->applicationMake('Subscribo\\Api1\\Factories\\AccountFactory');
        $found = $accountFactory->find($this->context->getServiceId(), $validated);
        if (empty($found)) {
            return ['validated' => false];
        }
        if ($accountFactory->checkCustomerPassword($found['customer'], $validated['password'])) {
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

    public function resumeRegistration(ActionInterruption $actionInterruption, array $data, Context $context, Questionary $questionary)
    {
        if ($data['service'] === 'new') {
            $extraData = $actionInterruption->extraData;
            $customerRegistration = CustomerRegistration::find($extraData['customerRegistrationId']);
            if (empty($customerRegistration)) {
                throw new RuntimeException(sprintf("CustomerRegistration with id '%s' not found", $extraData['customerRegistrationId']));
            }
            $actionInterruption->answer = $data;
            return $this->performRegistration($customerRegistration, $context->getServiceId(), $actionInterruption);
        }
        throw new \RuntimeException('Not implemented');
        //todo implement

    }

    public function resumeOAuthMissingEmail(ActionInterruption $actionInterruption, array $data, Context $context, Questionary $questionary)
    {
        $extraData = $actionInterruption->extraData;
        $customerRegistration = CustomerRegistration::find($extraData['customerRegistrationId']);
        if (empty($customerRegistration)) {
            throw new RuntimeException(sprintf("CustomerRegistration with id '%s' not found", $extraData['customerRegistrationId']));
        }
        $emailUsed = $this->checkEmailUsed($data['email'], $context->getServiceId(), $customerRegistration);
        $customerRegistration->email = $data['email'];
        $customerRegistration->save();
        if ($emailUsed) {
            $extraData = ['customerRegistrationId' => $customerRegistration->id];
            return $this->askQuestion(Questionary::CODE_LOGIN_OR_NEW_ACCOUNT, $extraData, 'resumeOAuthExistingEmail');
        }
        $actionInterruption->answer = $data;
        return $this->performRegistration($customerRegistration, $context->getServiceId(), $actionInterruption);
    }

    public function resumeOAuthExistingEmail(ActionInterruption $actionInterruption, array $data, Context $context, Questionary $questionary)
    {
        throw new \RuntimeException('NOT IMPLEMENTED');
        //todo implement
    }
    private function generateCustomerRegistration(array $data, $serviceId)
    {
        /** @var CustomerRegistrationFactory $customerRegistrationFactory */
        $customerRegistrationFactory = $this->applicationMake('Subscribo\\Api1\\Factories\\CustomerRegistrationFactory');
        return $customerRegistrationFactory->generate($data, $serviceId);
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
     * @param CustomerRegistration|array $data
     * @param int $serviceId
     * @param ActionInterruption $actionInterruption
     * @return array
     */
    private function performRegistration($data, $serviceId, ActionInterruption $actionInterruption = null)
    {
        /** @var \Subscribo\Api1\Factories\AccountFactory $accountFactory */
        $accountFactory = $this->applicationMake('Subscribo\\Api1\\Factories\\AccountFactory');

        $registered = $accountFactory->register($data, $serviceId);

        if ($actionInterruption) {
            $actionInterruption->status = ActionInterruption::STATUS_PROCESSED;
            $actionInterruption->save();
        }
        return  ['registered' => true, 'result' => $registered ];
    }


    private function oAuthRegistration(array $data, $serviceId)
    {
        $validatedOAuthData = $this->validateOAuthData($data);
        $alreadyRegistered = AccountToken::findByIdentifierAndServiceId($validatedOAuthData['identifier'], $serviceId);
        if ($alreadyRegistered) {
            return ['registered' => true, 'result' => $this->assembleAccountResult($alreadyRegistered->account)];
        }
        if (empty($data['email']) or ( ! $this->isEmailAcceptable($data['email']))) {
            $customerRegistration = $this->generateCustomerRegistration($data, $serviceId);
            $extraData = ['customerRegistrationId' => $customerRegistration->id];
            return $this->askQuestion(Questionary::CODE_NEW_CUSTOMER_EMAIL, $extraData, 'resumeOAuthMissingEmail');
        }
        $emailUsed = $this->checkEmailUsed($data['email'], $serviceId, $data);
        if ($emailUsed) {
            $customerRegistration = $this->generateCustomerRegistration($data, $serviceId);
            $extraData = ['customerRegistrationId' => $customerRegistration->id];
            return $this->askQuestion(Questionary::CODE_LOGIN_OR_NEW_ACCOUNT, $extraData, 'resumeOAuthExistingEmail');
        }
        return $this->performRegistration($data, $serviceId);
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
        $availableProvidersRule = AbstractOAuthManager::getAvailableDrivers();
        array_unshift($availableProvidersRule, 'in');
        $oAuthData = $data['oauth'];
        $rules = [
            'identifier'    => 'required|max:255',
            'provider'      => ['required', $availableProvidersRule],
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
     * @param CustomerRegistration|array $data
     * @return bool
     */
    private function checkEmailUsed($email, $serviceId, $data)
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
        $serviceList = array();
        foreach ($compatibleServices as $serviceIdToAdd) {
            $service = Service::find($serviceIdToAdd);
            $serviceList[$serviceIdToAdd] = $service->name;
        }
        $additionalData = ['samePoolServices' => $serviceList];
        $questionary = QuestionaryFactory::make(Questionary::CODE_MERGE_OR_NEW_ACCOUNT, $additionalData);

        $customerRegistration = ($data instanceof CustomerRegistration) ? $data : $this->generateCustomerRegistration($data, $serviceId);
        $extraData = ['customerRegistrationId' => $customerRegistration->id];
        $this->askQuestion($questionary, $extraData, 'resumeRegistration');
    }

    private function isEmailAcceptable($email)
    {
        $domain = trim(strtolower(strstr($email, '@')));
        if ('@facebook.com' === $domain) {
            return false;
        }
        return true;
    }

}
