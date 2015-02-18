<?php namespace Subscribo\ApiClientOAuth\Providers;

use Laravel\Socialite\Two\FacebookProvider as Base;
use Subscribo\ApiClientOAuth\Traits\ResponseErrorsTrait;

/**
 * Class FacebookProvider
 *
 * @package Subscribo\ApiClientOAuth
 */
class FacebookProvider extends Base
{
    use ResponseErrorsTrait;

    protected function getAuthUrl($state)
    {
        $url = 'https://www.facebook.com/'.$this->version.'/dialog/oauth';
        return $this->buildAuthUrlFromBase($url, $state);
    }
}
