<?php namespace Subscribo\ApiClientCommon\Traits;

use Exception;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Subscribo\ApiClientCommon\Connectors\ServerRequestConnector;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException;
use Subscribo\Exception\Exceptions\InvalidIdentifierHttpException;
use Subscribo\Exception\Exceptions\RuntimeException;


/**
 * Trait ClientRedirectionControllerTrait
 *
 * @package Subscribo\ApiClientCommon
 */
trait ClientRedirectionControllerTrait
{
    use HandleServerRequestExceptionTrait;

    /**
     * Action method getClientRedirectionRedirectingBack
     * for catching redirection from other server, passing query to API and redirecting to uri specified in Session
     *
     * @param Request $request
     * @param Store $session
     * @param ServerRequestConnector $connector
     * @param string|null $hash
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException
     * @throws \Subscribo\Exception\Exceptions\RuntimeException
     */
    public function getClientRedirectionRedirectingBack(Request $request, Store $session, ServerRequestConnector $connector, $hash = null)
    {
        $hash = $this->validateHashFormat($hash);
        /** @var ClientRedirection $clientRedirection */
        $clientRedirection = $session->pull($this->sessionKeyClientRedirection);
        $redirectTo = $session->pull($this->sessionKeyRedirectFromClientRedirection);
        if (empty($clientRedirection) or empty($redirectTo)) {
            throw new SessionVariableNotFoundHttpException();
        }
        if ($hash) {
            if ($clientRedirection->hash !== $hash) {
                throw new RuntimeException("getClientRedirectionRedirectingBack(): Hash mismatch");
            }
        }
        $data = $request->query->all();

        try {
            $response = $connector->postAnswer($clientRedirection, $data, true);
        } catch (ServerRequestException $e) {
            return $this->handleServerRequestException($e, $redirectTo);
        } catch (ValidationErrorsException $e) {
            return redirect($redirectTo)->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            $this->logException($e);
            return redirect($redirectTo)->withErrors(['Some error has happened. Please try again later or contact an administrator.']);
        }
        return redirect($redirectTo)->with($this->sessionKeyServerRequestHandledResult, $response);
    }

    /**
     * Action method for getting Redirection from API and redirecting to specified URL if possible
     * (Possibly not redirecting but displays error page)
     *
     * @param Request $request
     * @param Store $session
     * @param ServerRequestConnector $connector
     * @param string $type
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getRedirectionByType(Request $request, Store $session, ServerRequestConnector $connector, $type)
    {
        try {
            $resultInSession = $session->pull($this->sessionKeyServerRequestHandledResult);
            $redirectionData = $resultInSession ? $connector->resumeGetRedirection($resultInSession)
                                                : $connector->getRedirection($type, $request->query->all(), true);
            $clientRedirection = new ClientRedirection($redirectionData);
            $url = $clientRedirection->getUrl();
        } catch (ServerRequestException $e) {
            return $this->handleServerRequestException($e, $request->getRequestUri());
        } catch (ValidationErrorsException $e) {
            return view('subscribo::apiclientcommon.errorsonly', ['errorList' => $e->getValidationErrors()]);
        } catch (Exception $e) {
            $this->logException($e);
            return view('subscribo::apiclientcommon.errorsonly', ['errorList' => ['Some error has happened. Please try again later or contact an administrator.']]);
        }
        return redirect($url);
    }

    /**
     * @param string|null $hash
     * @return string|null
     * @throws \Subscribo\Exception\Exceptions\InvalidIdentifierHttpException
     */
    protected function validateHashFormat($hash)
    {
        if (is_null($hash)) {
            return null;
        }
        $result = filter_var($hash, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#[A-Za-z0-9]+#']]);
        if (empty($result)) {
            throw new InvalidIdentifierHttpException();
        }
        return $result;
    }

}
