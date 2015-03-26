<?php namespace Subscribo\ApiClientAuth\Traits;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Exception;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use Subscribo\Localization\LocaleUtils;

/**
 * Class AuthenticatesAndRegistersUsersTrait
 *
 * @package Subscribo\ApiClientAuth
 */
trait AuthenticatesAndRegistersUsersTrait
{
    use ValidatesRequests;
    use AuthenticatesAndRegistersUsers;
    use HandleServerRequestExceptionTrait;

    public function getRegister(Registrar $registrar, Store $session, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit)
    {
        $resultInSession = $session->pull($this->sessionKeyServerRequestHandledResult);
        $account = $resultInSession ? $registrar->resumeAttempt($resultInSession) : null;
        if ($account) {
            $this->auth->login($account);
            LocaleUtils::rememberLocaleForUser($account, $sessionDeposit, $cookieDeposit);
            return redirect($this->redirectPath());
        }
        return view('auth.register');
    }


    public function postRegister(Registrar $registrar, Request $request, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit)
    {
        $rules = $registrar->getValidationRules();
        $this->validate($request, $rules);

        try {
            $account = $registrar->attempt($request->only(array_keys($rules)));
            if (empty($account)) {
                throw new Exception('Empty account.');
            }
        } catch (ServerRequestException $e) {
            return $this->handleServerRequestException($e, $request->url());

        } catch (ValidationErrorsException $e) {
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            $this->logException($e);
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors('Registration attempt failed. Please try again later or contact an administrator.');
        }
        $this->auth->login($account);
        LocaleUtils::rememberLocaleForUser($account, $sessionDeposit, $cookieDeposit);
        return redirect($this->redirectPath());
    }

    public function postLogin(Request $request, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit)
    {
        $this->validate($request, ['email' => 'required', 'password' => 'required']);

        $credentials = $request->only(['email', 'password']);

        try {
            $authenticated = $this->auth->attempt($credentials, $request->has('remember'), true);
        } catch (Exception $e) {
            return redirect($this->loginPath())
                ->withInput($request->only('email'))
                ->withErrors('Login attempt failed. Please try again later or contact an administrator.');
        }
        if ($authenticated) {
            LocaleUtils::rememberLocaleForUser($this->auth->user(), $sessionDeposit, $cookieDeposit);
            return redirect()->intended($this->redirectPath());
        }
        return redirect($this->loginPath())
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }
}
