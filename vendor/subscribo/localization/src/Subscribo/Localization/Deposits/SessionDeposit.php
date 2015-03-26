<?php namespace Subscribo\Localization\Deposits;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Subscribo\Localization\Interfaces\LocaleDepositInterface;

/**
 * Class SessionDeposit
 *
 * @package Subscribo\Localization
 */
class SessionDeposit implements LocaleDepositInterface
{
    /** @var string default key name for storing (selected) locale in session */
    const DEFAULT_SESSION_KEY_NAME = 'subscribo.localization.deposit.session.locale';

    /** @var SessionInterface  */
    protected $session;

    /** @var string  */
    protected $sessionKeyName = self::DEFAULT_SESSION_KEY_NAME;

    public function __construct(SessionInterface $session, $sessionKeyName = true)
    {
        $this->session = $session;
        if (true === $sessionKeyName) {
            $this->sessionKeyName = $this::DEFAULT_SESSION_KEY_NAME;
        } else {
            $this->sessionKeyName = $sessionKeyName;
        }
    }

    public function getLocale()
    {
        $locale = $this->session->get($this->sessionKeyName);
        return $locale;
    }

    public function setLocale($locale)
    {
        $this->session->set($this->sessionKeyName, $locale);
    }
}
