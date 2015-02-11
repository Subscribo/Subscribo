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

    /**
     * @param string $rememberToken
     * @param int $id
     * @param int $serviceId
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function findRemembered($rememberToken, $id, $serviceId)
    {
        $query = self::query();
        $query->where('id', $id)
            ->where('remember_token', $rememberToken)
            ->where('service_id', $serviceId);
        return $query->first();
    }

}
