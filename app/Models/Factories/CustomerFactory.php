<?php namespace Subscribo\App\Model\Factories;

use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\App\Model\Customer;
use Subscribo\App\Model\Account;
use Subscribo\App\Model\Person;
use Subscribo\Support\Arr;

class CustomerFactory
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

    public function register($serviceId, array $data)
    {
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
        return $result;
    }

    public function find($serviceId, array $data)
    {
        $serviceId = intval($serviceId);
        if (empty($data['email'])) {
            throw new InvalidArgumentException('CustomerFactory::find() Data should contain email');
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
