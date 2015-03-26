<?php namespace Subscribo\ApiClientLocalization\Traits;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Session\Store;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use Subscribo\ApiClientLocalization\LocalePossibilities;

trait LocaleAjaxTrait
{
    use ValidatesRequests;

    public function postLocaleAjax(Request $request, LocalePossibilities $possibilities, LocalizerInterface $localizer, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, Store $session)
    {
        $session->reflash();
        $uriStubs = $possibilities->getAvailableUriStubs();
        $rules = ['locale' => 'required|in:'.implode(',', $uriStubs)];
        $this->validate($request, $rules);
        $selectedLocale = $request->input('locale');
        $locale = $possibilities->getLocaleByUriStub($selectedLocale);
        $localizer->setLocale($locale);
        $sessionDeposit->setLocale($locale);
        $cookieDeposit->setLocale($locale);
        $result = [
            'changed' => true,
            'result' => [
                'locale' => $selectedLocale,
            ]
        ];
        return $result;
    }
}
