<?php namespace Subscribo\Api1\Factories;

use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\AccountToken;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\CustomerRegistration;
use Subscribo\Support\Arr;

class AccountFactory
{
    protected $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function create(array $data = array())
    {
        if (array_key_exists('password', $data)) {
            $hashedPassword = $this->hasher->make($data['password']);
            $data['password'] = $hashedPassword;
        }
        $customer = new Customer($data);
        return $customer;
    }

    /**
     * @param CustomerRegistration|array $data
     * @param int $serviceId
     * @return array
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public function register($data, $serviceId)
    {
        if ($data instanceof CustomerRegistration) {
            return $this->registerFromCustomerRegistration($data, $serviceId);
        }
        if ( ! is_array($data)) {
            throw new InvalidArgumentException('AccountFactory::register() data have to be either array or instance of CustomerRegistration');
        }
        $name = trim(Arr::get($data, 'name')) ?: Arr::get($data, 'email');
        $person = Person::generate($name, Arr::get($data, 'gender'));
        $customer = $this->create($data);
        $customer->person()->associate($person);
        $customer->save();
        $account = Account::generate($customer->id, $serviceId);
        $result = [
            'customer' => $customer,
            'account' => $account,
            'person' => $person,
        ];
        if ( ! empty($data['oauth'])) {
            AccountToken::generate($data['oauth'], $account->id);
        }
        return $result;
    }

    public function registerFromCustomerRegistration(CustomerRegistration $customerRegistration, $serviceId)
    {
        $name = trim($customerRegistration->name) ?: $customerRegistration->email;
        $person = Person::generate($name);//todo add gender if implemented
        $customer = new Customer();
        $customer->email = $customerRegistration->email;
        $customer->password = $customerRegistration->password;
        $customer->person()->associate($person);
        $customer->save();
        $account = Account::generate($customer->id, $serviceId);
        $result = [
            'customer' => $customer,
            'account' => $account,
            'person' => $person,
        ];
        if ($customerRegistration->accountTokenId) {
            $accountToken = AccountToken::find($customerRegistration->accountTokenId);
            if ($accountToken) {
                $accountToken->accountId = $account->id;
                $accountToken->save();
            }
        }
        return $result;
    }

    public function find($serviceId, array $data)
    {
        $serviceId = intval($serviceId);
        if (empty($data['email'])) {
            throw new InvalidArgumentException('AccountFactory::find() Data should contain email');
        }
        $customers = Customer::findAllByEmail($data['email']);
        foreach($customers as $customer) {
            foreach ($customer->accounts as $account) {
                if ($account->serviceId === $serviceId) {
                    return ['customer' => $customer, 'account' => $account, 'person' => $customer->person];
                }
            }
        }
        return null;
    }

    public function setCustomerPassword(Customer $customer, $newPassword)
    {
        $customer->password = $this->hasher->make($newPassword);
    }

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
