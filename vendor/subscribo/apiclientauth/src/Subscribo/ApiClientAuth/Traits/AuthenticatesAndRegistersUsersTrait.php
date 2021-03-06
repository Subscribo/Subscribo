<?php namespace Subscribo\ApiClientAuth\Traits;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Exception;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\ApiClientAuth\Traits\HandleUserRegistrationTrait;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use Subscribo\Localization\LocaleUtils;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class AuthenticatesAndRegistersUsersTrait
 *
 * expects class using this trait use also Illuminate\Foundation\Validation\ValidatesRequests;
 *
 * @package Subscribo\ApiClientAuth
 */
trait AuthenticatesAndRegistersUsersTrait
{
    use AuthenticatesAndRegistersUsers;
    use HandleServerRequestExceptionTrait;
    use HandleUserRegistrationTrait;

    public function getRegister(Guard $auth, Registrar $registrar, Store $session, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit)
    {
        $resultInSession = $session->pull($this->sessionKeyServerRequestHandledResult);
        $handled = $this->handleUserRegistrationResume($resultInSession, $auth, $registrar, $sessionDeposit, $cookieDeposit);
        if ($handled) {

            return redirect($this->redirectPath());
        }
        return view('auth.register');
    }


    public function postRegister(Guard $auth, Registrar $registrar, Request $request, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LocalizerInterface $localizer)
    {
        $registrationResult = $this->handleUserRegistration($auth, $registrar, $request, $sessionDeposit, $cookieDeposit, $localizer);
        if (isset($registrationResult['redirect'])) {

            return $registrationResult['redirect'];
        }

        return redirect($this->redirectPath());
    }

    public function postLogin(Guard $auth, Request $request, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LocalizerInterface $localizer)
    {
        $this->validate($request, ['email' => 'required', 'password' => 'required']);

        $credentials = $request->only(['email', 'password']);

        try {
            $authenticated = $auth->attempt($credentials, $request->has('remember'), true);
        } catch (ValidationErrorsException $e) {
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            $errorMessage = $localizer->trans('errors.loginFailed', [], 'apiclientauth::messages');
            return redirect($this->loginPath())
                ->withInput($request->only('email'))
                ->withErrors($errorMessage);
        }
        if ($authenticated) {
            LocaleUtils::rememberLocaleForUser($auth->user(), $sessionDeposit, $cookieDeposit);
            return redirect()->intended($this->redirectPath());
        }
        $errorMessage = $localizer->trans('errors.wrongCredentials', [], 'apiclientauth::messages');
        return redirect($this->loginPath())
            ->withInput($request->only('email'))
            ->withErrors(['email' => $errorMessage]);
    }
}
