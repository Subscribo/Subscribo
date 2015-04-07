<?php namespace Subscribo\ApiClientOAuth\ViewComposers;

use Illuminate\View\View;
use Subscribo\OAuthCommon\AbstractOAuthManager;
use Subscribo\Localization\Interfaces\LocalizerInterface;

class LoginWithButtonsComposer
{
    /** @var LocalizerInterface  */
    protected $localizer;

    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer->template('messages', 'apiclientoauth');
    }
    public function compose(View $view)
    {
        $providerNames = AbstractOAuthManager::getProviderName();
        $links = [];
        foreach ($providerNames as $driver => $name)
        {
            $url = route('subscribo.oauth.login', ['driver' => $driver]);
            $links[$url] = $this->localizer->trans('buttons.label', ['{name}' => $name]);
        }
        $view->with('oAuthLinks', $links);
    }
}
