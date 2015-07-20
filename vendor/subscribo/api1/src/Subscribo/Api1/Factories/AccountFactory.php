<?php namespace Subscribo\Api1\Factories;

use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\AccountToken;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\CustomerRegistration;
use Subscribo\ModelCore\Models\Service;
use Subscribo\Support\Arr;

/**
 * Class AccountFactory
 *
 * @package Subscribo\Api1
 */
class AccountFactory
{
    protected $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @param array $data
     * @return Customer
     */
    public function create(array $data = array())
    {
        if (array_key_exists('password', $data)) {
            $hashedPassword = $this->hasher->make($data['password']);
            $data['password'] = $hashedPassword;
        }
        $customer = new Customer(Arr::only($data, ['email', 'password', 'username']));
        return $customer;
    }

    /**
     * @param CustomerRegistration|array $data
     * @param int $serviceId
     * @param string $registrationLocaleIdentifier
     * @return array
     * @throws InvalidArgumentException
     */
    public function register($data, $serviceId, $registrationLocaleIdentifier)
    {
        if ($data instanceof CustomerRegistration) {
            return $this->registerFromCustomerRegistration($data, $serviceId, $registrationLocaleIdentifier);
        }
        if ( ! is_array($data)) {
            throw new InvalidArgumentException('AccountFactory::register() data have to be either array or instance of CustomerRegistration');
        }
        $account = Account::findByEmailAndServiceId($data['email'], $serviceId);
        if ($account) {
            $customer = $account->customer;
            $person = $customer->person;
        } else {
            /** @var Service $service */
            $service = Service::with('availableLocales', 'defaultLocale')->find($serviceId);
            $locale = $service->chooseLocale($registrationLocaleIdentifier);
            $person = Person::generate($data);
            $customer = $this->create($data);
            $customer->person()->associate($person);
            $customer->preferredLocale()->associate($locale->uncustomize());
            $customer->save();
            $account = Account::generate($customer, $service, $locale);
        }
        if ( ! empty($data['oauth'])) {
            AccountToken::generate($data['oauth'], $account->id);
        }
        $result = [
            'customer' => $customer,
            'account' => $account,
            'person' => $person,
        ];
        return $result;
    }

    /**
     * @param CustomerRegistration $customerRegistration
     * @param int $serviceId
     * @param string $registrationLocaleIdentifier
     * @return array
     */
    public function registerFromCustomerRegistration(CustomerRegistration $customerRegistration, $serviceId, $registrationLocaleIdentifier)
    {
        $data = $customerRegistration->export();
        $account = Account::findByEmailAndServiceId($customerRegistration->email, $serviceId);
        if ($account) {
            $status = $customerRegistration::STATUS_EXISTING_ACCOUNT_USED;
            $customer = $account->customer;
            $person = $customer->person;
        } else {
            $existingCustomerId = $customerRegistration->customerId;
            /** @var Service $service */
            $service = Service::with('availableLocales', 'defaultLocale')->find($serviceId);
            if ($existingCustomerId) {
                $status = $customerRegistration::STATUS_MERGED;
                /** @var Customer $customer */
                $customer = Customer::find($existingCustomerId);
                $locale = $service->chooseLocale($customer->preferredLocale->identifier);
                $person = $customer->person;
            } else {
                $locale = $service->chooseLocale($registrationLocaleIdentifier);
                $status = $customerRegistration::STATUS_NEW_ACCOUNT_GENERATED;
                $person = Person::generate($data);
                $customer = new Customer();
                $customer->email = $customerRegistration->email;
                $customer->password = $customerRegistration->password;
                $customer->preferredLocale()->associate($locale->uncustomize());
                $customer->person()->associate($person);
                $customer->save();
            }
            $account = Account::generate($customer, $service, $locale);
        }
        if ($customerRegistration->accountTokenId) {
            $accountToken = AccountToken::find($customerRegistration->accountTokenId);
            if ($accountToken) {
                $accountToken->accountId = $account->id;
                $accountToken->save();
            }
        }
        $customerRegistration->finalize($account, $status);
        $result = [
            'customer' => $customer,
            'account' => $account,
            'person' => $person,
        ];
        return $result;
    }

    /**
     * @param string $email
     * @param string|int $serviceId
     * @return array|null
     */
    public function findAccountByEmailAndServiceId($email, $serviceId)
    {
        $serviceId = strval($serviceId);
        $customers = Customer::findAllByEmail($email);
        foreach($customers as $customer) {
            foreach ($customer->accounts as $account) {
                if (strval($account->serviceId) === $serviceId) {
                    return ['customer' => $customer, 'account' => $account, 'person' => $customer->person];
                }
            }
        }
        return null;
    }

    /**
     * @param Customer $customer
     * @param string $newPassword
     */
    public function setCustomerPassword(Customer $customer, $newPassword)
    {
        $customer->password = $this->hasher->make($newPassword);
    }

    /**
     * @param Customer $customer
     * @param string $passwordToCheck
     * @return bool
     */
    public function checkCustomerPassword(Customer $customer, $passwordToCheck)
    {
        if (empty($passwordToCheck)) {
            return false;
        }
        if (empty($customer->password)) {
            return false;
        }
        return $this->hasher->check($passwordToCheck, $customer->password);
    }
}
