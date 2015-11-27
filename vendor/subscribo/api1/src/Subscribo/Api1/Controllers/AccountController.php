<?php

namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractController;
use Subscribo\Api1\Factories\AccountFactory;
use Subscribo\Api1\Factories\ContactFactory;
use Subscribo\Api1\Factories\CustomerRegistrationFactory;
use Subscribo\Api1\Factories\ClientRedirectionFactory;
use Subscribo\Api1\Exceptions\RuntimeException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\NoAccountHttpException;
use Subscribo\Exception\Exceptions\ValidationErrorsHttpException;
use Subscribo\Exception\Exceptions\WrongAccountHttpException;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;
use Subscribo\Exception\Exceptions\InvalidIdentifierHttpException;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\AccountToken;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\ModelCore\Models\Contact;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\CustomerRegistration;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\ServiceModule;
use Subscribo\ModelCore\Models\ServicePool;
use Subscribo\OAuthCommon\AbstractOAuthManager;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException;
use Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException;
use Subscribo\Support\Arr;

/**
 * Class AccountController
 *
 * API v1 Controller class handling customer registration, customer credentials validation,
 * getting account details, handling remember-me tokens and account merging
 *
 * @package Subscribo\Api1
 */
class AccountController extends AbstractController
{
    private $loginValidationRules = [
        'email' => 'required|email|max:255',
        'password' => 'required|min:5',
    ];

    private $registrationValidationRules = [
        'name'  => 'max:255',
        'gender' => 'in:man,woman',
        'first_name' => 'max:100',
        'last_name' => 'required_with:city|max:100',
        'email' => 'required_without:oauth|email|max:255',
        'password' => 'required_without:oauth|min:5',
        'oauth' => 'array',
    ];


    public function actionPostRegistration()
    {
        $rules = $this->registrationValidationRules;
        $validated = $this->validateRequestBody($rules);
        $serviceId = $this->context->getServiceId();
        if ( ! empty($validated['oauth'])) {
            return $this->oAuthRegistration($validated, $serviceId);
        }
        $emailUsed = $this->checkEmailUsed($validated['email'], $serviceId, $validated);
        if ($emailUsed) {
            throw new InvalidInputHttpException(['email' => $this->localizeError('postRegistration.emailUsed')]);
        }
        return $this->performRegistration($validated, $serviceId);
    }

    /**
     * GET Action validating customer credentials
     *
     * @return array
     * @throws InvalidQueryHttpException
     */
    public function actionGetValidation()
    {
        $validated = $this->validateRequestQuery($this->loginValidationRules);
        return $this->processValidation($validated, $this->context->getServiceId());
    }

    /**
     * POST Action validating customer credentials
     *
     * @return array
     * @throws InvalidInputHttpException
     */
    public function actionPostValidation()
    {
        $validated = $this->validateRequestBody($this->loginValidationRules);
        return $this->processValidation($validated, $this->context->getServiceId());
    }


    /**
     * GET Action for retrieving remember-me token
     *
     * @param string|null $accountAccessToken
     * @return array
     * @throws InvalidIdentifierHttpException
     * @throws InvalidQueryHttpException
     */
    public function actionGetRemembered($accountAccessToken = null)
    {
        $accountAccessToken = is_null($accountAccessToken) ? $this->context->getAccountAccessToken() : $accountAccessToken;
        $validated = $this->validateRequestQuery([
            'token' => 'required',
        ]);
        /** @var Account $account */
        $account = Account::findRemembered($validated['token'], $accountAccessToken, $this->context->getServiceId());
        if (empty($account)) {
            throw new InvalidQueryHttpException(['token' => $this->localizeError('getRemembered.accountNotFound')]);
        }

        return ['found' => true, 'result' => $this->assembleAccountResult($account, true)];
    }

    /**
     * PUT Action for setting remember-me token
     *
     * @param string|null $accountAccessToken
     * @return array
     * @throws InvalidInputHttpException
     */
    public function actionPutRemembered($accountAccessToken = null)
    {
        $validated = $this->validateRequestBody([
            'token' => 'required_without:forget',
            'forget' => 'boolean',
        ]);
        $account = $this->retrieveAccount($accountAccessToken);
        $account->rememberToken = $validated['token'] ?: null;
        $account->save();

        return ['remembered' => true, 'result' => $this->assembleAccountResult($account, true)];
    }

    /**
     * GET Action method for getting customer account details
     *
     * @param string|null $accountAccessToken
     * @return array
     * @throws InstanceNotFoundHttpException
     */
    public function actionGetDetail($accountAccessToken = null)
    {
        $account = $this->retrieveAccount($accountAccessToken);

        return ['found' => true, 'result' => $this->assembleAccountResult($account, true)];
    }

    /**
     * GET Action method to get address(es) for logged in customer
     *
     * @param int|null $id
     * @return array
     * @throws \Subscribo\Exception\Exceptions\NoAccountHttpException
     * @throws \Subscribo\Exception\Exceptions\WrongAccountHttpException
     * @throws \Subscribo\Exception\Exceptions\InstanceNotFoundHttpException
     */
    public function actionGetContact($id = null)
    {
        $account = $this->context->getAccount();
        if (empty($account)) {
            throw new NoAccountHttpException();
        }
        $customer = $account->customer;

        if (empty($id)) {
            $collection = [];
            foreach ($customer->contacts as $contact) {
                $collection[] = $this->enhanceContact($contact, $customer);
            }

            return ['collection' => $collection];
        }
        $contact = Contact::find($id);
        if (empty($contact)) {
            throw new InstanceNotFoundHttpException();
        }
        if ($contact->customerId !== $customer->id) {
            throw new WrongAccountHttpException();
        }

        return ['instance' => $this->enhanceContact($contact, $customer)];
    }

    /**
     * GET Action method to get address(es) for logged in customer
     *
     * @param int|null $addressId
     * @return array
     * @throws \Subscribo\Exception\Exceptions\NoAccountHttpException
     * @throws \Subscribo\Exception\Exceptions\WrongAccountHttpException
     * @throws \Subscribo\Exception\Exceptions\InstanceNotFoundHttpException
     * @deprecated
     * @todo remove
     */
    public function actionGetAddress($addressId = null)
    {
        $account = $this->context->getAccount();
        if (empty($account)) {
            throw new NoAccountHttpException();
        }
        $customer = $account->customer;
        $defaultShippingAddressId = $customer->getDefaultShippingAddressId();
        $defaultBillingAddressId = $customer->getDefaultBillingAddressId();

        if ($addressId) {
            $address = Address::find($addressId);
            if (empty($address)) {
                throw new InstanceNotFoundHttpException();
            }
            if ($address->customerId !== $customer->id) {
                throw new WrongAccountHttpException();
            }
            $item = $address->toArray();
            $item['is_default_shipping'] = ($address->id === $defaultShippingAddressId);
            $item['is_default_billing'] = ($address->id === $defaultBillingAddressId);

            return ['found' => true, 'result' => $item];
        }
        $result = [];
        foreach ($customer->addresses as $address) {
            $item = $address->toArray();
            $item['is_default_shipping'] = ($address->id === $defaultShippingAddressId);
            $item['is_default_billing'] = ($address->id === $defaultBillingAddressId);
            $result[$address->id] = $item;
        }

        return ['listed' => true, 'result' => $result];
    }

    /**
     * RESUME method to be called after Questionary::CODE_MERGE_OR_NEW_ACCOUNT have been sent to client
     *
     * @param ActionInterruption $actionInterruption
     * @param array $answer
     * @return array
     * @throws RuntimeException
     * @throws ValidationErrorsHttpException
     * @throws ClientRedirectionServerRequestHttpException
     * @throws \RuntimeException as consistency check in markMergeProposed()
     */
    public function resumeRegistration(ActionInterruption $actionInterruption, array $answer)
    {
        $extraData = $actionInterruption->extraData;
        $customerRegistration = $this->retrieveCustomerRegistration($extraData);
        if ($answer['service'] === 'new') {
            $actionInterruption->answer = $answer;
            return $this->performRegistration($customerRegistration, $this->context->getServiceId(), $actionInterruption);
        }
        $serviceToMergeId = strval($answer['service']);
        if (false === array_search($serviceToMergeId, $extraData['compatibleServices'], true)) {
            throw new ValidationErrorsHttpException(['service' => $this->localizeError('resumeRegistration.serviceInvalid')]);
        }
        $additionalData = ['serviceId' => $serviceToMergeId];
        $extraData['allowedServiceIds'] = [$serviceToMergeId];
        $extraData['originalRequestLocale'] = $this->context->getLocalizer()->getLocale();
        $clientRedirection = $this->prepareClientRedirection(ClientRedirection::CODE_CONFIRM_MERGE_REQUEST, $extraData, 'resumeMergeConfirmation', $additionalData);
        $customerRegistration->markMergeProposed($serviceToMergeId, $clientRedirection->hash);
        throw new ClientRedirectionServerRequestHttpException($clientRedirection);
    }

    /**
     * RESUME method to be called after Client have been redirected using ClientRedirection::CODE_CONFIRM_MERGE_REQUEST
     * This method can be called on original (merge request sending) or other (merge request accepting) server
     *
     * @param ActionInterruption $actionInterruption
     * @param array $data
     * @param string $action
     * @return array
     * @throws ValidationErrorsHttpException
     * @throws QuestionaryServerRequestHttpException
     * @throws RuntimeException
     */
    public function resumeMergeConfirmation(ActionInterruption $actionInterruption, array $data, $action)
    {
        if ('getRedirectionByHash' === $action) { //Merge request accepting server
            throw $this->makeAccountMergingConfirmationQuestionaryException($actionInterruption, $data);
        }
        if ('postRedirection' === $action) { //Merge request sending server after other server have answered
            return $this->processAccountMergeConfirmedOrRejected($actionInterruption, $data);
        }
        throw new RuntimeException(sprintf('AccountController::resumeMergeConfirmation(): Wrong action: %s', $action));
    }

    /**
     * RESUME method to be used on merge request accepting server dealing with Questionary form results
     *
     * @param ActionInterruption $actionInterruption
     * @param array $answer
     * @return array
     * @throws WrongAccountHttpException
     * @throws InvalidInputHttpException
     * @throws RuntimeException
     * @throws \RuntimeException as consistency check in markMergeRejected() and markMergeConfirmed()
     */
    public function resumeConfirmMergeAnswer(ActionInterruption $actionInterruption, array $answer)
    {
        $extraData = $actionInterruption->extraData;
        $additionalData = ['url' => $extraData['redirectBack']];
        $customerRegistration = $this->retrieveCustomerRegistration($extraData);
        $serviceId = $this->context->getServiceId();
        if ($answer['merge'] !== 'yes') {
            $this->switchToOriginalRequestLocale($extraData);
            $additionalData['query'] = ['error' => $this->localizeError('resumeConfirmMergeAnswer.rejected')];
            $clientRedirection = ClientRedirectionFactory::make(ClientRedirection::CODE_CONFIRM_MERGE_RESPONSE, $additionalData);
            $customerRegistration->markMergeRejected($serviceId);
            $actionInterruption->markAsProcessed($answer);
            return ['result' => $clientRedirection];
        }
        $currentAccount = $this->context->getAccount(true);
        if ($currentAccount and ($currentAccount->customer->email === $customerRegistration->email)) {
            $customerId = $currentAccount->customerId;
        } else {
            $email = $customerRegistration->email;
            $validatedCustomer = $this->findAndValidateCustomerAccount($email, $answer['password'], $serviceId);
            if (empty($validatedCustomer['customer']->id)) {
                $serviceName = $this->context->getService()->name;
                throw new InvalidInputHttpException([
                    'password' => $this->localizeError(
                        'resumeConfirmMergeAnswer.wrongPassword',
                        ['%email%' => $email, '{service}' => $serviceName]
                    )]);
            }
            $customerId = $validatedCustomer['customer']->id;
        }
        $this->switchToOriginalRequestLocale($extraData);
        $additionalData['query'] = ['result' => $this->localizeError('resumeConfirmMergeAnswer.confirmed')]; //Note: Not actually an error, but it was simpler to have resumeConfirmMergeAnswer responses together
        $clientRedirection = ClientRedirectionFactory::make(ClientRedirection::CODE_CONFIRM_MERGE_RESPONSE, $additionalData);
        $customerRegistration->markMergeConfirmed($serviceId, $customerId);
        $actionInterruption->markAsProcessed($answer);
        return ['result' => $clientRedirection];
    }

    /**
     * RESUME method for situation, when acceptable email was missing after OAuth registration
     * (Questionary::CODE_NEW_CUSTOMER_EMAIL or Questionary::CODE_LOGIN_OR_NEW_ACCOUNT)
     *
     * @param ActionInterruption $actionInterruption
     * @param array $answer
     * @return array
     * @throws QuestionaryServerRequestHttpException
     * @throws InvalidInputHttpException
     */
    public function resumeOAuthMissingEmail(ActionInterruption $actionInterruption, array $answer)
    {
        $validatedData = $actionInterruption->extraData['validatedData'];
        $validatedData['email'] = $answer['email'];
        if ( ! $this->isEmailAcceptable($validatedData['email'])) {
            throw new InvalidInputHttpException(['email' => $this->localizeError('resumeOAuthMissingEmail.unacceptableEmail')]);
        }
        $serviceId = $this->context->getServiceId();
        $emailUsed = $this->checkEmailUsed($validatedData['email'], $serviceId, $validatedData);
        if ($emailUsed) {
            throw $this->makeQuestion(Questionary::CODE_LOGIN_OR_NEW_ACCOUNT, ['validatedData' => $validatedData], 'resumeOAuthExistingEmail', ['%email%' => $validatedData['email']]);
        }
        $actionInterruption->answer = $answer;
        return $this->performRegistration($validatedData, $serviceId, $actionInterruption);
    }

    /**
     * @param ActionInterruption $actionInterruption
     * @param array $answer
     * @return array
     * @throws QuestionaryServerRequestHttpException
     * @throws InvalidInputHttpException
     */
    public function resumeOAuthExistingEmail(ActionInterruption $actionInterruption, array $answer)
    {
        if (empty($answer['password'])) {
            //User has actually provided a (new) email, so we would proceed as if the email was missing or was not acceptable
            return $this->resumeOAuthMissingEmail($actionInterruption, $answer);
        }
        $serviceId = $this->context->getServiceId();
        $validatedData = $actionInterruption->extraData['validatedData'];
        $result = $this->findAndValidateCustomerAccount($validatedData['email'], $answer['password'], $serviceId);
        if (empty($result)) {
            throw new InvalidInputHttpException(
                ['password' => $this->localizeError(
                    'resumeOAuthExistingEmail.invalidPassword',
                    ['%email%' => $validatedData['email']]
                )]);
        }
        $actionInterruption->answer = $answer;
        return $this->performRegistration($validatedData, $serviceId, $actionInterruption);
    }

    /**
     * @param Contact $contact
     * @param Customer $customer
     * @return array
     */
    private function enhanceContact(Contact $contact, Customer $customer)
    {
        $result = $contact->toArray();
        $item['is_default_shipping'] = ($customer->customerConfiguration->defaultShippingDetail
            and ($contact->id === $customer->customerConfiguration->defaultShippingDetail->contactId));
        $item['is_default_billing'] = ($customer->customerConfiguration->defaultBillingDetail
            and ($contact->id === $customer->customerConfiguration->defaultBillingDetail->contactId));

        return $result;
    }

    /**
     * @param array $extraData
     */
    private function switchToOriginalRequestLocale(array $extraData)
    {
        if (empty($extraData['originalRequestLocale'])) {
            return;
        }
        $this->context->getLocalizer()->setLocale($extraData['originalRequestLocale']);
    }

    /**
     * @param ActionInterruption $actionInterruption
     * @param array $queryData
     * @return QuestionaryServerRequestHttpException
     * @throws WrongAccountHttpException
     * @throws InvalidQueryHttpException
     */
    private function makeAccountMergingConfirmationQuestionaryException(ActionInterruption $actionInterruption, array $queryData)
    {
        if (empty($queryData['redirect_back'])) {
            throw new InvalidQueryHttpException(['redirect_back' => $this->localizeError('resumeMergeConfirmation.redirectBackMissing')]);
        }
        $extraData = $actionInterruption->extraData;
        $customerRegistration = $this->retrieveCustomerRegistration($extraData);
        $currentAccount = $this->context->getAccount(true);
        $omitPassword = ($currentAccount and ($currentAccount->customer->email === $customerRegistration->email));
        $locale = $this->context->getLocale();
        if (( ! $omitPassword) and ( ! empty($extraData['originalRequestLocale']))) {
            $localeObject = $this->context->getService()->chooseLocale($extraData['originalRequestLocale']);
            $locale = $localeObject ? $localeObject->identifier : $locale;
        }
        $this->context->getLocalizer()->setLocale($locale);
        $extraData['redirectBack'] = $queryData['redirect_back'];
        $requestingService = Service::find($actionInterruption->serviceId);
        $additionalData = [
            '{confirmingService}' => $this->context->getService()->translateIfTranslatable('name', $locale),
            '{requestingService}' => $requestingService->translateIfTranslatable('name', $locale),
            '%email%'    => $customerRegistration->email,
            'locale'     => $locale,
        ];
        if ($omitPassword) {
            $extraData['omitPasswordCheck'] = strval($this->context->getServiceId());
            return $this->makeQuestion(Questionary::CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE, $extraData, 'resumeConfirmMergeAnswer', $additionalData);
        }
        return $this->makeQuestion(Questionary::CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD, $extraData, 'resumeConfirmMergeAnswer', $additionalData);
    }

    /**
     * @param ActionInterruption $actionInterruption
     * @param array $data
     * @return array
     * @throws RuntimeException
     * @throws ValidationErrorsHttpException
     */
    private function processAccountMergeConfirmedOrRejected(ActionInterruption $actionInterruption, array $data)
    {
        $this->checkAccountMergeConfirmedInputData($data); //If merging was rejected, Exception is thrown
        $extraData = $actionInterruption->extraData;
        $customerRegistration = $this->retrieveCustomerRegistration($extraData);
        $this->checkAccountMergeConfirmedCustomerRegistration($customerRegistration);
        $actionInterruption->answer = $data;
        return $this->performRegistration($customerRegistration, $this->context->getServiceId(), $actionInterruption);
    }

    /**
     * Throws exception if account merge was rejected or result is missing
     *
     * @param array $data
     * @throws ValidationErrorsHttpException
     */
    private function checkAccountMergeConfirmedInputData(array $data)
    {
        $errors = Arr::get($data, 'errors', array());
        if ( ! empty($data['error'])) {
            $errors[] = $data['error'];
        }
        if (empty($errors) and empty($data['result'])) {
            $errors = [$this->localizeError('accountMergeConfirmed.resultMissing')];
        }
        if ($errors) {
            throw new ValidationErrorsHttpException($errors);
        }
    }

    /**
     * Additional validation and consistency check
     * Throws exception if account merge was not confirmed
     *
     * @param CustomerRegistration $customerRegistration
     * @throws RuntimeException
     * @throws ValidationErrorsHttpException
     */
    private function checkAccountMergeConfirmedCustomerRegistration(CustomerRegistration $customerRegistration)
    {
        if ($customerRegistration->status !== $customerRegistration::STATUS_MERGE_CONFIRMED) {
            throw new ValidationErrorsHttpException([$this->localizeError('accountMergeConfirmed.notConfirmed')]);
        }
        if (empty($customerRegistration->mergedToServiceId)) {
            throw new RuntimeException('checkAccountMergingPossiblyConfirmedCustomerRegistration() : mergedToServiceId empty');
        }
        if (empty($customerRegistration->customerId)) {
            throw new RuntimeException('checkAccountMergingPossiblyConfirmedCustomerRegistration() : customerId empty');
        }
        if ( ! ServicePool::servicesAreInSamePool($customerRegistration->serviceId, $customerRegistration->mergedToServiceId)) {
            throw new RuntimeException('checkAccountMergingPossiblyConfirmedCustomerRegistration() : services are not in the same pool');
        }
    }

    /**
     * @param array $extraData
     * @return CustomerRegistration
     * @throws RuntimeException
     */
    private function retrieveCustomerRegistration(array $extraData)
    {
        if (empty($extraData['customerRegistrationId'])) {
            throw new RuntimeException('retrieveCustomerRegistration(): customerRegistrationId empty');
        }
        $result = CustomerRegistration::find($extraData['customerRegistrationId']);
        if (empty($result)) {
            throw new RuntimeException('retrieveCustomerRegistration(): customerRegistration not found');
        }
        return $result;
    }

    /**
     * @param array $data
     * @param int|string $serviceId
     * @return CustomerRegistration
     */
    private function generateCustomerRegistration(array $data, $serviceId)
    {
        /** @var CustomerRegistrationFactory $customerRegistrationFactory */
        $customerRegistrationFactory = $this->applicationMake('Subscribo\\Api1\\Factories\\CustomerRegistrationFactory');
        return $customerRegistrationFactory->generate($data, $serviceId);
    }

    /**
     * @param string|null $accountAccessToken
     * @return Account
     * @throws InstanceNotFoundHttpException
     */
    private function retrieveAccount($accountAccessToken)
    {
        $accountAccessToken = is_null($accountAccessToken) ? $this->context->getAccountAccessToken() : $accountAccessToken;
        $account = Account::findByAccountAccessToken($accountAccessToken);
        if (empty($account)) {
            throw new InstanceNotFoundHttpException();
        }
        $this->context->checkServiceForAccount($account);

        return $account;
    }

    /**
     * @param Account $account
     * @param bool $addAccessToken
     * @return array
     */
    private function assembleAccountResult(Account $account, $addAccessToken = false)
    {
        $person = $account->customer ? $account->customer->person : null;
        $result = [
            'account' => $account->export($addAccessToken),
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

        $registered = $accountFactory->register($data, $serviceId, $this->context->getLocale());

        if ($actionInterruption) {
            $actionInterruption->markAsProcessed();
        }
        return ['registered' => true, 'result' => $registered ];
    }

    /**
     * @param array $data
     * @param int|string $serviceId
     * @return array
     * @throws InvalidInputHttpException
     * @throws QuestionaryServerRequestHttpException
     */
    private function oAuthRegistration(array $data, $serviceId)
    {
        $validatedOAuthData = $this->validateOAuthData($data);
        $data['oauth'] = $validatedOAuthData;
        $alreadyRegistered = AccountToken::findByOAuthDataAndServiceId($validatedOAuthData, $serviceId);
        if ($alreadyRegistered) {
            return ['registered' => true, 'result' => $this->assembleAccountResult($alreadyRegistered->account, true)];
        }
        if (empty($data['email']) or ( ! $this->isEmailAcceptable($data['email']))) {
            unset($data['password']);
            throw $this->makeQuestion(Questionary::CODE_NEW_CUSTOMER_EMAIL, ['validatedData' => $data], 'resumeOAuthMissingEmail');
        }
        $emailUsed = $this->checkEmailUsed($data['email'], $serviceId, $data);
        if ($emailUsed) {
            unset($data['password']);
            throw $this->makeQuestion(Questionary::CODE_LOGIN_OR_NEW_ACCOUNT, ['validatedData' => $data], 'resumeOAuthExistingEmail', ['%email%' => $data['email']]);
        }
        return $this->performRegistration($data, $serviceId);
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidInputHttpException
     */
    private function validateOAuthData(array $data)
    {
        if (empty($data['oauth']) or ( ! is_array($data['oauth']))) {
            throw new InvalidInputHttpException(['oauth' => $this->localizeError('postRegistration.oAuthInvalid')]);
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
     * @param string|int $serviceId
     * @param CustomerRegistration|array $data
     * @return bool
     * @throws QuestionaryServerRequestHttpException
     */
    private function checkEmailUsed($email, $serviceId, $data)
    {
        $alreadyRegisteredCustomers = Customer::findAllByEmail($email);
        if (empty($alreadyRegisteredCustomers)) {
            return false;
        }
        $serviceId = strval($serviceId);
        $service = Service::with('servicePools')->find($serviceId);
        $servicePools = $service->servicePools;
        $compatibleServices = [];

        foreach ($alreadyRegisteredCustomers as $customer) {
            foreach ($customer->accounts as $account) {
                $serviceIdToCheck = strval($account->serviceId);
                if ($serviceId === $serviceIdToCheck) {
                    return true;
                }
                if (ServicePool::isInPool($servicePools, $serviceIdToCheck)
                    and ServiceModule::isModuleEnabled($serviceIdToCheck, ServiceModule::MODULE_ACCOUNT_MERGING)) {
                        $compatibleServices[] = $serviceIdToCheck;
                }
            }
        }
        if (empty($compatibleServices)) {
            return false;
        }
        if ( ! ServiceModule::isModuleEnabled($serviceId, ServiceModule::MODULE_ACCOUNT_MERGING)) {
            return false;
        }
        throw $this->prepareResumeRegistrationQuestion($compatibleServices, $serviceId, $data);
    }

    /**
     * @param array $compatibleServices
     * @param int|string $serviceId
     * @param CustomerRegistration|array $data
     * @return QuestionaryServerRequestHttpException
     */
    private function prepareResumeRegistrationQuestion(array $compatibleServices, $serviceId, $data)
    {
        $localizer = $this->context->getLocalizer();
        $serviceList = [];
        foreach ($compatibleServices as $serviceIdToAdd) {
            $service = Service::find($serviceIdToAdd);
            $serviceList[$serviceIdToAdd] = $localizer->simplify($service->name);
        }
        $additionalData = ['samePoolServices' => $serviceList];

        $customerRegistration = ($data instanceof CustomerRegistration) ? $data : $this->generateCustomerRegistration($data, $serviceId);
        $extraData = [
            'customerRegistrationId' => $customerRegistration->id,
            'compatibleServices' => array_values($compatibleServices),
        ];
        return $this->makeQuestion(Questionary::CODE_MERGE_OR_NEW_ACCOUNT, $extraData, 'resumeRegistration', $additionalData);
    }

    /**
     * @param $email
     * @return bool
     */
    private function isEmailAcceptable($email)
    {
        $domain = trim(strtolower(strstr($email, '@')));
        if ('@facebook.com' === $domain) {
            return false;
        }
        return true;
    }

    /**
     * @param array $validated
     * @param int|string $serviceId
     * @return array
     */
    private function processValidation(array $validated, $serviceId)
    {
        $found = $this->findAndValidateCustomerAccount($validated['email'], $validated['password'], $serviceId);
        if (empty($found)) {
            return ['validated' => false];
        }
        return ['validated' => true, 'result' => $found];
    }

    /**
     * @param string $email
     * @param string $password
     * @param int|string $serviceId
     * @return array|bool|null
     */
    private function findAndValidateCustomerAccount($email, $password, $serviceId)
    {
        /** @var \Subscribo\Api1\Factories\AccountFactory $accountFactory */
        $accountFactory = $this->applicationMake('Subscribo\\Api1\\Factories\\AccountFactory');
        $found = $accountFactory->findAccountByEmailAndServiceId($email, $serviceId, true);
        if (empty($found['customer'])) {
            return null;
        }
        if ($accountFactory->checkCustomerPassword($found['customer'], $password)) {
            return $found;
        }
        return false;
    }

    private function localizeError($key, $parameters = array())
    {
        $localizer = $this->context->getLocalizer();
        return $localizer->trans('account.errors.'.$key, $parameters, 'api1::controllers');
    }
}
