<?php

namespace Subscribo\ModelCore\Models;

/**
 * Model AccountToken
 *
 * Model class for being changed and used in the application
 */
class AccountToken extends \Subscribo\ModelCore\Bases\AccountToken
{
    /**
     * todo - test
     *
     * @param array $oAuthData
     * @param int $serviceId
     * @return AccountToken|null|bool
     */
    public static function findByOAuthDataAndServiceId(array $oAuthData, $serviceId)
    {
        /** @var AccountToken $instance */
        $instance = static::query()
            ->where('identifier', $oAuthData['identifier'])
            ->where('provider', $oAuthData['provider'])
            ->whereHas('account', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })->first();

        if (empty($instance)) {

            return null;
        }
        $token = isset($oAuthData['token']) ? strval($oAuthData['token']) : '';
        $secret = isset($oAuthData['secret']) ? strval($oAuthData['secret']) : '';
        if ($instance->token and (strval($instance->token) !== $token)) {

            return false;
        }
        if ($instance->secret and (strval($instance->secret) !== $secret)) {

            return false;
        }

        return $instance;
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
