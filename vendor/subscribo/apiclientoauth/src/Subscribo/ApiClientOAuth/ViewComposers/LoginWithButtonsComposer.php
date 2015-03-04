<?php namespace Subscribo\ApiClientOAuth\ViewComposers;

use Illuminate\View\View;
use Subscribo\OAuthCommon\AbstractOAuthManager;

class LoginWithButtonsComposer
{
    public function compose(View $view)
    {
        $providerNames = AbstractOAuthManager::getProviderName();
        $providers = [];
        foreach ($providerNames as $driver => $name)
        {
            $url = route('subscribo.oauth.login', ['driver' => $driver]);
            $providers[$url] = $name;
        }
        $view->with('providers', $providers);
    }
}
