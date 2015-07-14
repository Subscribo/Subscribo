<?php namespace Subscribo\OAuthCommon\Providers;

use Laravel\Socialite\Two\FacebookProvider as Base;
use Subscribo\OAuthCommon\Traits\TwoResponseErrorsTrait;

/**
 * Class FacebookProvider
 *
 * @package Subscribo\OAuthCommon
 */
class FacebookProvider extends Base
{
    use TwoResponseErrorsTrait;

    protected function getAuthUrl($state)
    {
        $url = 'https://www.facebook.com/'.$this->version.'/dialog/oauth';
        return $this->buildAuthUrlFromBase($url, $state);
    }
}
