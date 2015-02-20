<?php namespace Subscribo\ModelCore\Models;


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
     * @param int $id
     * @param int $serviceId
     * @return Account|static|null
     */
    public static function findRemembered($rememberToken, $id, $serviceId)
    {
        $query = static::query();
        $query->where('id', $id)
            ->where('remember_token', $rememberToken)
            ->where('service_id', $serviceId);
        return $query->first();
    }

}
