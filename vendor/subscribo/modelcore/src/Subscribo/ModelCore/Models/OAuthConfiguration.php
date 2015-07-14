<?php namespace Subscribo\ModelCore\Models;


/**
 * Model OAuthConfiguration
 *
 * Model class for being changed and used in the application
 */
class OAuthConfiguration extends \Subscribo\ModelCore\Bases\OAuthConfiguration {

    /**
     * @param $provider
     * @param $serviceId
     * @return OAuthConfiguration|static|null
     */
    public static function findByProviderAndServiceId($provider, $serviceId)
    {
        $query = static::query();
        $query->where('provider', $provider)
            ->where('service_id', $serviceId);
        $result = $query->first();
        return $result;
    }

}
