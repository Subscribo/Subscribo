<?php namespace Subscribo\ApiClientOAuth;

use Subscribo\OAuthCommon\AbstractOAuthManager;
use Subscribo\ApiClientOAuth\Connectors\OAuthConnector;

/**
 * Class OAuthManager
 *
 * @package Subscribo\ApiClientOAuth
 */
class OAuthManager extends AbstractOAuthManager
{
    protected $driverConfigurations = array();

    protected $defaultScopes = array();

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
}
