<?php namespace Subscribo\ApiClientAuth\Traits;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Exception;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\ApiClientAuth\QuestionList;
use Subscribo\ApiClientAuth\Exceptions\ValidationException;
use Illuminate\Contracts\Auth\Authenticatable;


trait AuthenticatesAndRegistersUsersTrait
{
    use ValidatesRequests;
    use AuthenticatesAndRegistersUsers;


    public function postRegister(Registrar $registrar, Request $request)
    {
        $rules = $registrar->getValidationRules();
        $this->validate($request, $rules);

        try {
            $response  = $registrar->attempt($request->only(array_keys($rules)));
            if (empty($response)) {
                throw new Exception('Empty response.');
            }
        } catch (ValidationException $e) {
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
        if ($response instanceof QuestionList) {
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors(['email' => 'This email has already been used for another service and account merging is not implemented yet. Please choose different email.']);
        }

        if ($response instanceof Authenticatable) {
            $this->auth->login($response);
            return redirect($this->redirectPath());
        }
        throw new Exception('Response is neither instance of QuestionList nor Authenticatable');
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
