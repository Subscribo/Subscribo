<?php namespace Subscribo\ModelCore\Models;

/**
 * Model AccountToken
 *
 * Model class for being changed and used in the application
 */
class AccountToken extends \Subscribo\ModelCore\Bases\AccountToken {

    /**
     * @param $identifier
     * @param $serviceId
     * @return AccountToken|static|null
     */
    public static function findByIdentifierAndServiceId($identifier, $serviceId)
    {
        $result = static::query()
            ->where('identifier', $identifier)
            ->whereHas('account', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })->first();
        return $result;
    }

    /**
     * @param array $data
     * @param int $accountId
     * @return AccountToken|static
     */
    public static function generate(array $data, $accountId)
    {
        $accountToken = new static(array_only($data, array('provider', 'identifier', 'token', 'secret')));
        $accountToken->accountId = $accountId;
        $accountToken->save();
        return $accountToken;
    }

}
