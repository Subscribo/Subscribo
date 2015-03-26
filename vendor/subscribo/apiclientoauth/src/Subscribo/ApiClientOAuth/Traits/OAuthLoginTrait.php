<?php namespace Subscribo\ApiClientOAuth\Traits;

use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\ApiClientOAuth\OAuthManager;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\Exception\Exceptions\NotFoundHttpException;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use Subscribo\Localization\LocaleUtils;

/**
 * Class OAuthLoginTrait
 *
 * @package Subscribo\ApiClientOAuth
 */
trait OAuthLoginTrait
{
    use HandleServerRequestExceptionTrait;

    /** @var string $redirectPath where to go after OAuth authentication (e.g.: '/home')  */
    protected $redirectPath = '/home';

    protected $registrationPath = '/auth/register';

    public function getLogin(OAuthManager $manager, $provider)
    {
        if (false === array_search($provider, $manager->getAvailableDrivers(), true)) {
            throw new NotFoundHttpException();
        }
        return $manager->assembleRedirect($provider);
    }


    public function getHandle(OAuthManager $manager, Registrar $registrar, Guard $auth, Request $request, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, $provider)
    {
        if (false === array_search($provider, $manager->getAvailableDrivers(), true)) {
            throw new NotFoundHttpException();
        }
        $error = null;
        try {
            $user = $manager->getUser($provider);
        } catch (Exception $e) {
            $error = 'There was an error during authorization by '.$manager->getProviderName($provider).'.';
        }
        if (empty($user)) {
            $error = 'You have probably rejected authorization by '.$manager->getProviderName($provider).'.';
        }
        if ($error) {
            $error .= ' Please try again or use a different form of login or registration.';
            return redirect($this->registrationPath)
                ->withErrors($error);
        }
        $token = isset($user->token) ? $user->token : null;
        $secret = isset($user->tokenSecret) ? $user->tokenSecret : null;
        $nameAndEmail = [
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
        ];
        $registrationData = $nameAndEmail;
        $registrationData['oauth'] = [
            'provider'      => $provider,
            'identifier'    => $user->getId(),
            'token'         => $token,
            'secret'        => $secret,
        ];
        try {
            $account = $registrar->attempt($registrationData);
            if (empty($account)) {
                throw new Exception('Empty account.');
            }
        } catch (ServerRequestException $e) {
            return $this->handleServerRequestException($e, $request->getUriForPath($this->registrationPath));
        } catch (ValidationErrorsException $e) {
            return redirect($this->registrationPath)
                ->withInput($nameAndEmail)
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            $this->logException($e);
            return redirect($this->registrationPath)
                ->withInput($nameAndEmail)
                ->withErrors('Login attempt failed. Please try again later or contact an administrator.');
        }
        $auth->login($account);
        LocaleUtils::rememberLocaleForUser($account, $sessionDeposit, $cookieDeposit);
        return redirect($this->redirectPath);
    }
}
