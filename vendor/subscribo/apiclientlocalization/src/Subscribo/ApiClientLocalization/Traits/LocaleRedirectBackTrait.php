<?php namespace Subscribo\ApiClientLocalization\Traits;

use Illuminate\Session\Store;
use Subscribo\Exception\Exceptions\NotFoundHttpException;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use Subscribo\ApiClientLocalization\LocalePossibilities;


trait LocaleRedirectBackTrait
{
    public function getLocaleRedirectBack(LocalePossibilities $possibilities, LocalizerInterface $localizer, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, Store $session, $selectedLocale)
    {
        $session->reflash();
        $uriStubs = $possibilities->getAvailableUriStubs();
        if (false === array_search($selectedLocale, $uriStubs, true)) {
            throw new NotFoundHttpException('Requested locale not defined');
        }
        $locale = $possibilities->getLocaleByUriStub($selectedLocale);
        $localizer->setLocale($locale);
        $sessionDeposit->setLocale($locale);
        $cookieDeposit->setLocale($locale);
        return redirect()->back();
    }
}
