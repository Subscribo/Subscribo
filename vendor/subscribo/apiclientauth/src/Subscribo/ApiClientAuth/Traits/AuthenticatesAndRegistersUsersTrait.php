<?php namespace Subscribo\ApiClientAuth\Traits;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Exception;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestHttpExceptionTrait;

/**
 * Class AuthenticatesAndRegistersUsersTrait
 *
 * @package Subscribo\ApiClientAuth
 */
trait AuthenticatesAndRegistersUsersTrait
{
    use ValidatesRequests;
    use AuthenticatesAndRegistersUsers;
    use HandleServerRequestHttpExceptionTrait;

    public function getRegister(Registrar $registrar, Store $session)
    {
        $resultInSession = $session->pull($this->sessionKeyQuestionaryAnswerResult);
        $account = $resultInSession ? $registrar->resumeAttempt($resultInSession) : null;
        if ($account) {
            $this->auth->login($account);
            return redirect($this->redirectPath());
        }
        return view('auth.register');
    }


    public function postRegister(Registrar $registrar, Request $request)
    {
        $rules = $registrar->getValidationRules();
        $this->validate($request, $rules);

        try {
            $account = $registrar->attempt($request->only(array_keys($rules)));
            if (empty($account)) {
                throw new Exception('Empty account.');
            }
        } catch (ServerRequestHttpException $e) {
            return $this->handleServerRequestHttpException($e, $request->path());

        } catch (ValidationErrorsException $e) {
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors('Registration attempt failed. Please try again later or contact an administrator.');
        }
        $this->auth->login($account);
        return redirect($this->redirectPath());
    }

    public function postLogin(Request $request)
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
            return redirect()->intended($this->redirectPath());
        }
        return redirect($this->loginPath())
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }
}
