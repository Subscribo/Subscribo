<?php namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractController;
use Subscribo\App\Model\Customer;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;
use Subscribo\App\Model\ServicePool;
use Subscribo\App\Model\Account;


class CustomerController extends AbstractController
{

    public function actionPostRegister()
    {
        $validated = $this->validateRequestBody([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
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
    public function actionGetValidate()
    {
        $validated = $this->validateRequestQuery([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
        /** @var \Subscribo\App\Model\Factories\CustomerFactory $customerFactory */
        $customerFactory = $this->applicationMake('Subscribo\\App\\Model\\Factories\\CustomerFactory');
        $found = $customerFactory->find($this->context->getServiceId(), $validated);
        if (empty($found)) {
            throw $this->assembleCredentialsNotValidException();
        }
        if ($customerFactory->checkCustomerPassword($found['customer'], $validated['password'])) {
            return ['validated' => $found];
        }
        throw $this->assembleCredentialsNotValidException();
    }

    public function actionPostRemember()
    {
        $validated = $this->validateRequestBody([
            'id' => 'required|integer',
            'token' => '',
        ]);
        /** @var Account $account */
        $account = Account::find($validated['id']);
        if (empty($account)) {
            throw new InvalidInputHttpException(['id' => 'Account not found']);
        }
        $account->rememberToken = $validated['token'] ?: null;
        $account->save();
        return ['remembered' => $account];
    }

    public function actionGetRetrieve()
    {
        $validated = $this->validateRequestQuery([
            'id' => 'required|integer',
            'token' => 'required',
        ]);
        /** @var Account $account */
        $account = Account::findByIdAndToken($validated['id'], $validated['token']);
        if (empty($account)) {
            throw new InvalidInputHttpException(['id' => 'Account not found', 'token' => 'Account not found']);
        }
        return ['found' => [
            'account' => $account,
            'customer' => $account->customer,
        ]];
    }

    protected function performRegistration(array $data, $serviceId)
    {
        /** @var \Subscribo\App\Model\Factories\CustomerFactory $customerFactory */
        $customerFactory = $this->applicationMake('Subscribo\\App\\Model\\Factories\\CustomerFactory');

        $registered = $customerFactory->register($serviceId, $data);
        $result = [
            'registered' => $registered,
            'accountId' => $registered['account']->id,
        ];
        return $result;
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
