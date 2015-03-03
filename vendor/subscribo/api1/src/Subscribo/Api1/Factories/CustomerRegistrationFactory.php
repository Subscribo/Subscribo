<?php namespace Subscribo\Api1\Factories;

use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\ModelCore\Models\CustomerRegistration;
use Subscribo\ModelCore\Models\AccountToken;

class CustomerRegistrationFactory
{
    protected $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function generate(array $data = array(), $serviceId)
    {
        $customerRegistration = new CustomerRegistration();
        $customerRegistration->serviceId = $serviceId;
        if (array_key_exists('password', $data)) {
            $customerRegistration->password = $this->hasher->make($data['password']);
        }
        if ( ! empty($data['name'])) {
            $customerRegistration->name = $data['name'];
        }
        if ( ! empty($data['email'])) {
            $customerRegistration->email = $data['email'];
        }
        if ( ! empty($data['oauth'])) {
            $token = AccountToken::generate($data['oauth'], null);
            $customerRegistration->accountTokenId = $token->id;
        }
        $customerRegistration->save();
        return $customerRegistration;
    }
}
