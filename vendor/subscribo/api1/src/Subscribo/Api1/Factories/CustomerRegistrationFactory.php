<?php namespace Subscribo\Api1\Factories;

use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\ModelCore\Models\CustomerRegistration;
use Subscribo\ModelCore\Models\AccountToken;
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
        if ( ! empty($data['name'])) {
            $customerRegistration->name = $data['name'];
        }
        if ( ! empty($data['email'])) {
            $customerRegistration->email = $data['email'];
        }
        if ( ! empty($data['gender'])) {
            $customerRegistration->gender = $data['gender'];
        }
        if ( ! empty($data['first_name'])) {
            $customerRegistration->firstName = $data['first_name'];
        }
        if ( ! empty($data['last_name'])) {
            $customerRegistration->lastName = $data['last_name'];
        }
        if ( ! empty($data['street'])) {
            $customerRegistration->street = $data['street'];
        }
        if ( ! empty($data['post_code'])) {
            $customerRegistration->postCode = $data['post_code'];
        }
        if ( ! empty($data['city'])) {
            $customerRegistration->city = $data['city'];
        }
        if ( ! empty($data['country'])) {
            $customerRegistration->country = $data['country'];
        }
        if ( ! empty($data['phone'])) {
            $customerRegistration->phone = $data['phone'];
        }
        if ( ! empty($data['delivery_information'])) {
            $customerRegistration->deliveryInformation = $data['delivery_information'];
        }
        if ( ! empty($data['oauth'])) {
            $token = AccountToken::generate($data['oauth'], null);
            $customerRegistration->accountTokenId = $token->id;
        }
        $customerRegistration->save();
        return $customerRegistration;
    }
}
