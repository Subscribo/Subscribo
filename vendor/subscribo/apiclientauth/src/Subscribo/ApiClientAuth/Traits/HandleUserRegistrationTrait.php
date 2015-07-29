<?php

namespace Subscribo\ApiClientAuth\Traits;

use Exception;
use RuntimeException;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\ApiClientAuth\Registrar;
use Illuminate\Http\Request;
use Subscribo\Localization\Deposits\CookieDeposit;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\LocaleUtils;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Psr\Log\LoggerInterface;

/**
 * Trait HandleUserRegistrationTrait
 * Following traits needs to be used on some level by trait/class using this trait:
 * \Illuminate\Foundation\Validation\ValidatesRequests
 * \Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait
 * @package Subscribo\ApiClientAuth\Traits
 */
trait HandleUserRegistrationTrait
{
    /**
     * @param Guard $auth
     * @param Registrar $registrar
     * @param Request $request
     * @param SessionDeposit $sessionDeposit
     * @param CookieDeposit $cookieDeposit
     * @param LocalizerInterface $localizer
     * @param LoggerInterface $logger
     * @param array $exceptInput
     * @throws \Illuminate\Http\Exception\HttpResponseException
     * @return array
     */
    protected function handleUserRegistration(Guard $auth, Registrar $registrar, Request $request, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LocalizerInterface $localizer, LoggerInterface $logger = null, $exceptInput = [])
    {
        $exceptInput = array_unique(array_merge($exceptInput, ['password', 'password_confirmation', '_token']));

        $rules = $registrar->getValidationRules();
        $this->validate($request, $rules);
        try {
            $data = array_intersect_key($request->request->all(), $rules);
            $rawResponse = $registrar->getRawRegistrationResponse($data);
            $account = $registrar->makeAuthenticatableModelFromRawRegistrationResponse($rawResponse);
            if (empty($account)) {
                throw new RuntimeException('Empty Account');
            }
        } catch(ServerRequestException $serverRequestException) {

            return ['redirect' => $this->handleServerRequestException($serverRequestException, $request->url())];
        } catch (ValidationErrorsException $validationErrorsException) {
            $errors = $validationErrorsException->getValidationErrors();
            $inputForRedirect = $request->except($exceptInput);

            return ['redirect' => redirect($request->url())->withInput($inputForRedirect)->withErrors($errors)];
        } catch (Exception $genericException) {
            $this->logException($genericException, $logger);
            $errorMessage = $localizer->trans('errors.registrationFailed', [], 'apiclientauth::messages');
            $inputForRedirect = $request->except($exceptInput);

            return ['redirect' => redirect($request->url())->withInput($inputForRedirect)->withErrors($errorMessage)];
        }
        $rememberMe = $request->request->get('remember_me');
        $auth->login($account, $rememberMe);
        $cookieDeposit = $rememberMe ? $cookieDeposit : null;
        LocaleUtils::rememberLocaleForUser($account, $sessionDeposit, $cookieDeposit);

        return [
            'account' => $account,
            'response' => $rawResponse,
        ];
    }
}
