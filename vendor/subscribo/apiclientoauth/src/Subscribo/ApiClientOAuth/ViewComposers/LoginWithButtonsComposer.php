<?php namespace Subscribo\ApiClientOAuth\ViewComposers;

use Illuminate\View\View;
use Subscribo\OAuthCommon\AbstractOAuthManager;

class LoginWithButtonsComposer
{
    public function compose(View $view)
    {
        $providers = AbstractOAuthManager::getProviderName();
        $view->with('providers', $providers);
        $view->with('baseUri', '/oauth/login/');
    }
}
