<?php

namespace Subscribo\ApiClientCommon\Traits;

use Exception;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Subscribo\ApiClientCommon\Connectors\ServerRequestConnector;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\Widget;
use Subscribo\Exception\Exceptions\RuntimeException;
use Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Trait WidgetControllerTrait
 *
 * @package Subscribo\ApiClientCommon
 */
trait WidgetControllerTrait
{
    use HandleServerRequestExceptionTrait;
    use ValidatesRequests;

    /**
     * Action method getQuestionaryFromSession for retrieving Questionary from Session and displaying it
     *
     * @param Store $session
     * @return \Illuminate\View\View
     * @throws \Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException
     */
    public function getWidgetFromSession(Store $session)
    {
        $widget = $session->get($this->sessionKeyWidgetServerRequest);
        if (empty($widget)) {
            throw new SessionVariableNotFoundHttpException;
        }
        $session->keep([$this->sessionKeyWidgetServerRequest, $this->sessionKeyRedirectFromWidget]);

        return view('subscribo::apiclientcommon.widget')->with('widget', $widget);
    }

    /**
     * Action method actionWidgetReturn
     * for catching POST and GET responses from other server, passing response data to API and redirecting to uri specified in Session
     *
     * @param Request $request
     * @param Store $session
     * @param ServerRequestConnector $connector
     * @param LocalizerInterface $localizer
     * @param null $hash
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException
     * @throws \Subscribo\Exception\Exceptions\RuntimeException
     */
    public function actionWidgetReturn(Request $request, Store $session, ServerRequestConnector $connector, LocalizerInterface $localizer, $hash = null)
    {
        /** @var Widget $widget */
        $widget = $session->pull($this->sessionKeyWidgetServerRequest);
        $redirectTo = $session->pull($this->sessionKeyRedirectFromWidget);
        if (empty($widget) or empty($redirectTo)) {
            throw new SessionVariableNotFoundHttpException();
        }
        if ($hash) {
            if ($widget->hash !== $hash) {
                throw new RuntimeException("actionWidgetReturn(): Hash mismatch");
            }
        }
        $data = [
            'request' => [
                'hash' => $hash,
                'method' => $request->method(),
                'uri' => $request->getUri(),
                'scheme' => $request->getScheme(),
                'host' => $request->getHost(),
                'port' => $request->getPort(),
                'path' => $request->getPathInfo(),
                'content' => $request->getContent(),
                'headers' => $request->headers->all(),
                'query' => $request->query->all(),
                'postData' => $request->request->all(),
            ],
        ];

        try {
            $response = $connector->postAnswer($widget, $data, true);
        } catch (ServerRequestException $e) {

            return $this->handleServerRequestException($e, $redirectTo);
        } catch (ValidationErrorsException $e) {

            return redirect($redirectTo)->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            $this->logException($e);
            $errorMessage = $localizer->trans('traits.widget.widgetReturn.errors.fallback', [], 'apiclientcommon::messages');

            return redirect($redirectTo)->withErrors([$errorMessage]);
        }

        return redirect($redirectTo)->with($this->sessionKeyServerRequestHandledResult, $response);
    }
}
