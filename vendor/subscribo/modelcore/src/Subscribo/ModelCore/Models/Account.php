<?php namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Customer;

/**
 * Model Account
 *
 * Model class for being changed and used in the application
 */
class Account extends \Subscribo\ModelCore\Bases\Account
{
    /**
     * @param int $customerId
     * @param int $serviceId
     * @return Account|static
     */
    public static function generate($customerId, $serviceId)
    {
        $account = new static();
        $account->customerId = $customerId;
        $account->serviceId = $serviceId;
        $account->save();
        return $account;
    }

    /**
     * @param string $rememberToken
     * @param int|string $id
     * @param int|string $serviceId
     * @return Account|null
     */
    public static function findRemembered($rememberToken, $id, $serviceId)
    {
        $query = static::query();
        $query->where('id', $id)
            ->where('remember_token', $rememberToken)
            ->where('service_id', $serviceId);
        return $query->first();
    }

    /**
     * @param string $email
     * @param int|string $serviceId
     * @return Account|null
     */
    public static function findByEmailAndServiceId($email, $serviceId)
    {
        $serviceId = strval($serviceId);
        $query = Customer::query()->where('email', $email)->with('accounts');
        $customers = $query->get();
        /** @var Customer $customer */
        foreach ($customers as $customer) {
            foreach($customer->accounts as $account) {
                if (strval($account->serviceId) === $serviceId) {
                    $account->setRelation('customer', $customer);
                    return $account;
                }
            }
        }
        return null;
    }

}
