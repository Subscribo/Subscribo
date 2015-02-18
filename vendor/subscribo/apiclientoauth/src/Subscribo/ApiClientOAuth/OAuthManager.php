<?php namespace Subscribo\ApiClientOAuth;

use Laravel\Socialite\SocialiteManager;
use Subscribo\ApiClientOAuth\Connectors\OAuthConnector;
use Subscribo\ApiClientOAuth\Providers\FacebookProvider;
use Subscribo\ApiClientOAuth\Exceptions\ErrorResponseException;

/**
 * Class OAuthManager
 *
 * @package Subscribo\ApiClientOAuth
 */
class OAuthManager extends SocialiteManager
{
    protected $driverConfigurations = array();

    protected $defaultScopes = array();


    public static function getAvailableDrivers()
    {
        return ['facebook', 'test'];
    }

    public function getDefaultDriver()
    {
        return 'facebook';
    }

    public function getDefaultScopes($driver)
    {
        if (isset($this->defaultScopes[$driver])) {
            return $this->defaultScopes[$driver];
        }
        $this->retrieveDriverConfiguration($driver);
        return $this->defaultScopes[$driver];
    }

    public function assembleRedirect($driver)
    {
        $scopes = $this->getDefaultScopes($driver);
        if (is_array($scopes)) {
            return $this->with($driver)->scopes($scopes)->redirect();
        }
        return $this->with($driver)->redirect();
    }

    public function getUser($driver)
    {
        try {
            return $this->with($driver)->user();
        } catch(ErrorResponseException $e) {
            return null;
        }
    }



    protected function getDriverConfiguration($driver)
    {
        if (isset($this->driverConfigurations[$driver])) {
            return $this->driverConfigurations[$driver];
        }
        $this->retrieveDriverConfiguration($driver);
        return $this->driverConfigurations[$driver];
    }


    protected function retrieveDriverConfiguration($driver)
    {
        /** @var OAuthConnector $oauthConnector */
        $oauthConnector = $this->app->make('Subscribo\\ApiClientOAuth\\Connectors\\OAuthConnector');
        $result = $oauthConnector->getConfig($driver);
        $this->driverConfigurations[$driver] = $result['config'];
        $this->defaultScopes[$driver] = isset($result['scopes']) ? $result['scopes'] : false;
        return $result;
    }


    /**
     * @return FacebookProvider
     */
    protected function createFacebookDriver()
    {
        $config = $this->getDriverConfiguration('facebook');
        return $this->buildProvider('Subscribo\\ApiClientOAuth\\Providers\\FacebookProvider', $config);
    }
}
