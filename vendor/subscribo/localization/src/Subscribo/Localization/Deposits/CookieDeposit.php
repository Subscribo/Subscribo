<?php namespace Subscribo\Localization\Deposits;

use Illuminate\Http\Request;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Subscribo\Localization\Interfaces\LocaleDepositInterface;
use Subscribo\Localization\Localizer;

/**
 * Class CookieDeposit
 *
 * @package Subscribo\Localization
 */
class CookieDeposit implements LocaleDepositInterface
{
    /** @var string Cookie name */
    const DEFAULT_COOKIE_NAME = 'locale';

    /** @var int Cookie expiration time in minutes, 0 for till end of browser session */
    const DEFAULT_EXPIRATION_PERIOD = 0;

    /** @var \Illuminate\Http\Request  */
    protected $request;

    /** @var \Illuminate\Contracts\Cookie\QueueingFactory  */
    protected $cookieJar;

    /** @var string  */
    protected $cookieName = self::DEFAULT_COOKIE_NAME;

    /** @var int */
    protected $expire = self::DEFAULT_EXPIRATION_PERIOD;

    public function __construct(Request $request, QueueingFactory $cookieJar, $cookieExpire = true, $cookieName = true)
    {
        $this->request = $request;
        $this->cookieJar = $cookieJar;
        if (true === $cookieName) {
            $this->cookieName = $this::DEFAULT_COOKIE_NAME;
        } else {
            $this->cookieName = $cookieName;
        }
        if (true === $cookieExpire) {
            $this->expire = $this::DEFAULT_EXPIRATION_PERIOD;
        } else {
            $this->expire = $cookieExpire;
        }
    }

    public function getLocale()
    {
        $cookieContent = $this->request->cookie($this->cookieName);
        $result = Localizer::parseLocaleDescription($cookieContent);
        return $result;
    }

    public function setLocale($locale)
    {
        $secure = $this->request->secure();
        $cookie = $this->cookieJar->make($this->cookieName, $locale, $this->expire, null, null, $secure, true);
        $this->cookieJar->queue($cookie);
    }
}
