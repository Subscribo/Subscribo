<?php namespace Subscribo\ApiClientOAuth\Traits;

use Exception;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\ApiClientOAuth\OAuthManager;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestHttpExceptionTrait;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\Exception\Exceptions\NotFoundHttpException;

/**
 * Class OAuthLoginTrait
 *
 * @package Subscribo\ApiClientOAuth
 */
trait OAuthLoginTrait
{
    use HandleServerRequestHttpExceptionTrait;

    protected $redirectPath = '/home';

    protected $registrationPath = '/auth/register';

    public function getLogin(OAuthManager $manager, $provider)
    {
        if (false === array_search($provider, $manager->getAvailableDrivers(), true)) {
            throw new NotFoundHttpException();
        }
        return $manager->assembleRedirect($provider);
    }


    public function getHandle(OAuthManager $manager, Registrar $registrar, Guard $auth, $provider)
    {
        if (false === array_search($provider, $manager->getAvailableDrivers(), true)) {
            throw new NotFoundHttpException();
        }
        $user = $manager->getUser($provider);
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
        } catch (ServerRequestHttpException $e) {
            return $this->handleServerRequestHttpException($e, $this->registrationPath);
        } catch (ValidationErrorsException $e) {
            return redirect($this->registrationPath)
                ->withInput($nameAndEmail)
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            return redirect($this->registrationPath)
                ->withInput($nameAndEmail)
                ->withErrors('Login attempt failed. Please try again later or contact an administrator.');
        }
        $auth->login($account);
        return redirect($this->redirectPath);
    }
}
