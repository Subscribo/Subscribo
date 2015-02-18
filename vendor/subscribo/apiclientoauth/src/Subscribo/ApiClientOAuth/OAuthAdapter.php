<?php namespace Subscribo\ApiClientOAuth;

use InvalidArgumentException;
use Subscribo\ApiClientOAuth\Connectors\OAuthConnector;
use League\OAuth2\Client\Provider\Facebook;

/**
 * Class OAuthAdapter
 *
 * Adapter for League OAuth2 client
 *
 * TODO remove (not used)
 *
 * @package Subscribo\ApiClientOAuth
 */
class OAuthAdapter
{
    /**
     * @var Connectors\OAuthConnector
     */
    protected $connector;

    public function __construct(OAuthConnector $connector)
    {
        $this->connector = $connector;
    }

    public static function getAvailableProviders()
    {
        return ['facebook'];
    }

    public function assembleRedirect($provider)
    {
        return $this->assembleProvider($provider)->getAuthorizationUrl();
    }

    /**
     * @param string $providerName
     * @return Facebook
     * @throws \InvalidArgumentException
     */
    public function assembleProvider($providerName)
    {
        $configuration = $this->connector->getConfig($providerName);
        switch($providerName)
        {
            case 'facebook':
                return new Facebook($configuration);
        }
        throw new InvalidArgumentException('OAuthAdapter::assembleProvider() invalid provider name.');

    }

}
