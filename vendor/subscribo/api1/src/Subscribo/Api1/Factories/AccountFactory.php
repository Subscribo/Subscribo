<?php namespace Subscribo\Api1\Factories;

use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\BillingDetail;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\AccountToken;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\CustomerConfiguration;
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
     * @param CustomerRegistration|array $data
     * @param Address|null $shippingAddress
     * @param Address|null $billingAddress
     * @return Customer
     */
    public function generateCustomer($data = array(), Address $shippingAddress = null, Address $billingAddress = null)
    {
        if ($data instanceof CustomerRegistration) {
            $customer = new Customer();
            $customer->email = $data->email;
            $customer->password = $data->password;
            $person = $data->person;
        } else {
            if (array_key_exists('password', $data)) {
                $hashedPassword = $this->hasher->make($data['password']);
                $data['password'] = $hashedPassword;
            }
            $customer = new Customer(Arr::only($data, ['email', 'password']));
            $person = Person::generate($data);
        }
        $customer->person()->associate($person);
        $customer->save();
        $configuration = new CustomerConfiguration();
        $configuration->customerId = $customer->id;
        if ($shippingAddress) {
            $shippingAddress->customerId = $customer->id;
            $shippingAddress->save();
            $configuration->defaultShippingAddress()->associate($shippingAddress);
        }
        if ($billingAddress) {
            $billingAddress->customerId = $customer->id;
            $billingAddress->save();
            $billingDetail = BillingDetail::generate($billingAddress);
            $configuration->defaultBillingDetail()->associate($billingDetail);
        }
        $configuration->save();

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
        $customerRegistration = null;
        $existingCustomerId = null;
        if ($data instanceof CustomerRegistration) {
            $customerRegistration = $data;
            $email = $customerRegistration->email;
            $existingCustomerId = $customerRegistration->customerId;
            $dataPossiblyWithAddress = $customerRegistration->address;
        } elseif (is_array($data)) {
            $email = $data['email'];
            $dataPossiblyWithAddress = $data;
        } else {
            throw new InvalidArgumentException('AccountFactory::register() data have to be either array or instance of CustomerRegistration');
        }
        $account = Account::findByEmailAndServiceId($email, $serviceId);
        if ($account) {
            $status = CustomerRegistration::STATUS_EXISTING_ACCOUNT_USED;
            $customer = $account->customer;
            $address = Address::ifDataContainsAddressFindSimilarOrGenerate($dataPossiblyWithAddress, $customer);
        } else {
            if ($existingCustomerId) {
                $status = CustomerRegistration::STATUS_MERGED;
                /** @var Customer $customer */
                $customer = Customer::find($existingCustomerId);
                $preferredLocale = $customer->preferredLocale->identifier;
            } else {
                $status = CustomerRegistration::STATUS_NEW_ACCOUNT_GENERATED;
                $customer = null;
                $preferredLocale = $registrationLocaleIdentifier;
            }
            /** @var Service $service */
            $service = Service::with('availableLocales', 'defaultLocale')->find($serviceId);
            $locale = $service->chooseLocale($preferredLocale);
            $address = Address::ifDataContainsAddressFindSimilarOrGenerate($dataPossiblyWithAddress, $customer);
            if (empty($customer)) {
                $customer = $this->generateCustomer($data, $address);
                $customer->preferredLocale()->associate($locale->uncustomize());
                $customer->save();
            }
            $account = Account::generate($customer, $service, $locale);
        }
        if ($customerRegistration) {
            $accountToken = $customerRegistration->accountToken;
            if ($accountToken) {
                $accountToken->accountId = $account->id;
                $accountToken->save();
            }
            $customerRegistration->finalize($account, $status);
        } elseif ( ! empty($data['oauth'])) {
            AccountToken::generate($data['oauth'], $account->id);
        }
        $result = [
            'customer' => $customer,
            'account' => $account,
            'person' => $customer->person,
            'address' => $address,
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

    /**
     * @param Customer $customer
     * @param Address $shippingAddress
     * @param Address|null $billingAddress
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public static function addAddressesIfNotPresent(Customer $customer, Address $shippingAddress = null, Address $billingAddress = null)
    {
        if ($shippingAddress) {
            if ($shippingAddress->customerId !== $customer->id) {
                throw new InvalidArgumentException('Provided shipping address does not have appropriate customer_id set');
            }
            if (empty($shippingAddress->id)) {
                throw new InvalidArgumentException('Provided shipping address has not been saved before');
            }
            if ( ! $customer->getDefaultShippingAddressId()) {
                $customer->customerConfiguration->defaultShippingAddress()->associate($shippingAddress);
                $customer->customerConfiguration->save();
            }
        }
        if ($billingAddress) {
            if ($billingAddress->customerId !== $customer->id) {
                throw new InvalidArgumentException('Provided billing address does not have appropriate customer_id set');
            }
            if (empty($billingAddress->id)) {
                throw new InvalidArgumentException('Provided billing address has not been saved before');
            }
            if ( ! $customer->getDefaultBillingAddressId()) {
                $billingDetail = BillingDetail::addAddressOrGenerate(
                    $billingAddress,
                    $customer->customerConfiguration->defaultBillingDetail
                );
                $customer->customerConfiguration->defaultBillingDetail()->associate($billingDetail);
                $customer->customerConfiguration->save();
            }
        }
    }
}
