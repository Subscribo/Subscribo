<?php namespace Subscribo\App\Model;


/**
 * Model Account
 *
 * Model class for being changed and used in the application
 */
class Account extends \Subscribo\App\Model\Base\Account
{
    public static function generate($customerId, $serviceId)
    {
        $account = new self();
        $account->customerId = $customerId;
        $account->serviceId = $serviceId;
        $account->save();
        return $account;
    }

    public static function findByIdAndToken($id, $token)
    {
        $query = self::query();
        $query->where('id', $id)
            ->where('remember_token', $token);
        return $query->first();
    }

}
