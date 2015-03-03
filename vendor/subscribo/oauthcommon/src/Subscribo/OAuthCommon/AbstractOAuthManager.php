<?php namespace Subscribo\OAuthCommon;

use Laravel\Socialite\SocialiteManager;
use Subscribo\OAuthCommon\Providers\FacebookProvider;
use Subscribo\OAuthCommon\Exceptions\ErrorResponseException;

/**
 * Abstract Class AbstractOAuthManager
 *
 * Children need to implement getDriverConfiguration() method
 *
 * @package Subscribo\OAuthCommon
 */
abstract class AbstractOAuthManager extends SocialiteManager
{
    /**
     * @return array
     */
    public static function getAvailableDrivers()
    {
        return ['facebook'];
    }

    public static function getProviderName($provider = null)
    {
        $providerNames = [
            'facebook' => 'Facebook',
            'twitter'  => 'Twitter',
        ];
        if (is_null($provider)) {
            return $providerNames;
        }
        return $providerNames[$provider];
    }

    /**
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'facebook';
    }

    /**
     * @param string $driver
     * @return array containing keys: client_id, client_secret and redirect
     */
    abstract protected function getDriverConfiguration($driver);

    /**
     * @param string $driver
     * @return null|\Laravel\Socialite\Contracts\User|\Laravel\Socialite\AbstractUser
     */
    public function getUser($driver)
    {
        try {
            return $this->with($driver)->user();
        } catch(ErrorResponseException $e) {
            return null;
        }
    }

    /**
     * @return FacebookProvider
     */
    protected function createFacebookDriver()
    {
        $config = $this->getDriverConfiguration('facebook');
        return $this->buildProvider('Subscribo\\OAuthCommon\\Providers\\FacebookProvider', $config);
    }
}
