<?php namespace Subscribo\Api1\Factories;

use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\ModelCore\Models\CustomerRegistration;
use Subscribo\ModelCore\Models\AccountToken;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Person;
use Subscribo\Support\Arr;

/**
 * Class CustomerRegistrationFactory
 *
 * @package Subscribo\Api1
 */
class CustomerRegistrationFactory
{
    protected $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @param array $data
     * @param int|string $serviceId
     * @return CustomerRegistration
     */
    public function generate(array $data = array(), $serviceId)
    {
        $customerRegistration = new CustomerRegistration();
        $customerRegistration->serviceId = $serviceId;
        $status = Arr::get($data, 'status', CustomerRegistration::STATUS_PREPARED);
        $customerRegistration->status = $status;
        if ( ! empty($data['password'])) {
            $customerRegistration->password = $this->hasher->make($data['password']);
        }
        if ( ! empty($data['email'])) {
            $customerRegistration->email = $data['email'];
        }
        if ( ! empty($data['oauth'])) {
            $token = AccountToken::generate($data['oauth'], null);
            $customerRegistration->accountTokenId = $token->id;
        }
        $person = Person::generate($data);
        $customerRegistration->personId = $person->id;
        $address = Address::ifDataContainsAddressFindSimilarOrGenerate($data);
        $customerRegistration->addressId = $address ? $address->id : null;
        $customerRegistration->save();
        return $customerRegistration;
    }
}
