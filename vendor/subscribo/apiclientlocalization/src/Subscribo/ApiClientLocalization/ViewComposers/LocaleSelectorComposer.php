<?php namespace Subscribo\ApiClientLocalization\ViewComposers;

use Illuminate\View\View;
use Illuminate\Routing\UrlGenerator;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\ApiClientLocalization\LocalePossibilities;


class LocaleSelectorComposer
{
    protected $localeChangeRouteName = 'subscribo.localization.setting.redirect';

    /** @var LocalePossibilities  */
    protected $possibilities;

    /** @var UrlGenerator  */
    protected $urlGenerator;

    /** @var LocalizerInterface  */
    protected $localizer;


    public function __construct(LocalePossibilities $possibilities, UrlGenerator $urlGenerator, LocalizerInterface $localizer)
    {
        $this->possibilities = $possibilities;
        $this->urlGenerator = $urlGenerator;
        $this->localizer = $localizer;
    }

    public function compose(View $view)
    {
        $locales = $this->possibilities->getAvailableLocalesWithUriStub();
        $data = [];
        $currentLocale = $this->localizer->getLocale();
        foreach ($locales as $uriStub => $locale) {
            $label = $this->possibilities->getLabel($locale, $currentLocale);
            $uri = $this->urlGenerator->route($this->localeChangeRouteName, ['locale' => $uriStub]);
            $data[$uri] = $label;
        }
        $view->with('localeLinks', $data);
    }

}
