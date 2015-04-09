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
        $drivers = AbstractOAuthManager::getAvailableDrivers();
        $links = [];
        foreach ($drivers as $driver)
        {
            $url = route('subscribo.oauth.login', ['driver' => $driver]);
            $id = 'buttons.label.specific.'.$driver;
            if ($this->localizer->canTranslate($id)) {
                $links[$url] = $this->localizer->trans($id);
            } else {
                $providerNameTranslateId = 'providers.name.'.$driver;
                $providerName = $this->localizer->canTranslate($providerNameTranslateId)
                    ? $this->localizer->trans($providerNameTranslateId)
                    : AbstractOAuthManager::getProviderName($driver);
                $links[$url] = $this->localizer->trans('buttons.label.fallback', ['{providerName}' => $providerName]);
            }
        }
        $view->with('oAuthLinks', $links);
    }
}
