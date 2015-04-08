<?php namespace Subscribo\OAuthCommon\Servers;

use League\OAuth1\Client\Server\Twitter;
use League\OAuth1\Client\Signature\SignatureInterface;
use Subscribo\Localization\LocaleTools;
/**
 * Class TwitterServer
 *
 * Extends ThePHPLeague OAuth1 Twitter driver with localization possibility
 * Made using ideas and/or code from extended class
 *
 * @license MIT
 *
 * @package Subscribo\OAuthCommon\Servers
 */
class TwitterServer extends Twitter
{
    protected $language;

    /**
     * @param array|\League\OAuth1\Client\Credentials\ClientCredentialsInterface $clientCredentials
     * @param SignatureInterface $signature
     * @param string|null $locale
     */
    public function __construct($clientCredentials, SignatureInterface $signature = null, $locale = null)
    {
        if ($locale) {
            $this->language = LocaleTools::localeTagToLanguage($locale);
        }
        parent::__construct($clientCredentials, $signature);
    }

    public function urlAuthorization()
    {
        $url = parent::urlAuthorization();
        if ($this->language) {
            $parameters = ['lang' => $this->language];
            $url = $this->buildUrl($url, http_build_query($parameters));
        }
        return $url;
    }
}
