<?php namespace Subscribo\ApiClientOAuth\Traits;

use Exception;
use LogicException;
use Subscribo\ApiClientAuth\Exceptions\ValidationException;
use Subscribo\ApiClientAuth\QuestionList;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\ApiClientOAuth\OAuthManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OAuthLoginTrait
 *
 * @package Subscribo\ApiClientOAuth
 */
trait OAuthLoginTrait
{
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
            $response  = $registrar->attempt($registrationData);
            if (empty($response)) {
                throw new Exception('Empty response.');
            }
        } catch (ValidationException $e) {
            return redirect($this->registrationPath)
                ->withInput($nameAndEmail)
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            return redirect($this->registrationPath)
                ->withInput($nameAndEmail)
                ->withErrors('Login attempt failed. Please try again later or contact an administrator.');
        }
        if ($response instanceof QuestionList) {
            return redirect($this->registrationPath)
                ->withInput($nameAndEmail)
                ->withErrors(['email' => 'This email has already been used for another service and account merging is not implemented yet. Please choose different email.']);
        }
        if ($response instanceof Authenticatable) {
            $auth->login($response);
            return redirect($this->redirectPath);
        }
        throw new LogicException('Response is neither instance of QuestionList nor Authenticatable');
    }

}