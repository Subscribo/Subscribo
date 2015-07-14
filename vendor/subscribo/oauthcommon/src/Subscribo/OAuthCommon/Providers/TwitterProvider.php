<?php namespace Subscribo\OAuthCommon\Providers;

use Laravel\Socialite\One\TwitterProvider as Base;
use Subscribo\OAuthCommon\Traits\OneResponseErrorsTrait;

class TwitterProvider extends Base
{
    use OneResponseErrorsTrait;
}
