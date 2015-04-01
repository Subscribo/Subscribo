<?php namespace Subscribo\ApiClientCommon\Traits;

use Exception;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Subscribo\ApiClientCommon\Connectors\ServerRequestConnector;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\Questionary;
use Subscribo\Exception\Exceptions\RuntimeException;
use Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Trait QuestionaryControllerTrait
 *
 * @package Subscribo\ApiClientCommon
 */
trait QuestionaryControllerTrait
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
    public function getQuestionaryFromSession(Store $session)
    {
        $questionary = $session->get($this->sessionKeyQuestionary);
        if (empty($questionary)) {
            throw new SessionVariableNotFoundHttpException;
        }
        $session->keep([$this->sessionKeyQuestionary, $this->sessionKeyRedirectFromQuestionary]);
        return view('subscribo::apiclientcommon.questionary')->with('questionary', $questionary);
    }

    /**
     * Action method getQuestionaryByType for retrieving questionary from API and displaying it
     *
     * @param Request $request
     * @param Store $session
     * @param ServerRequestConnector $connector
     * @param LocalizerInterface $localizer
     * @param string $type
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getQuestionaryByType(Request $request, Store $session, ServerRequestConnector $connector, LocalizerInterface $localizer, $type)
    {
        try {
            $questionaryData = $connector->getQuestionary($type, $request->query->all(), true);
            $questionary = new Questionary($questionaryData);
        } catch (ServerRequestException $e) {
            return $this->handleServerRequestException($e, $request->getRequestUri());
        } catch (ValidationErrorsException $e) {
            return view('subscribo::apiclientcommon.errorsonly', ['errorList' => $e->getValidationErrors()]);
        } catch (Exception $e) {
            $this->logException($e);
            $errorMessage = $localizer->trans('traits.questionary.getQuestionary.errors.fallback', [], 'apiclientcommon::messages');
            return view('subscribo::apiclientcommon.errorsonly', ['errorList' => [$errorMessage]]);
        }
        $session->flash($this->sessionKeyQuestionary, $questionary);
        return view('subscribo::apiclientcommon.questionary')->with('questionary', $questionary);
    }


    /**
     * Action method postQuestionary for POSTing Questionary to API
     *
     * @param Request $request
     * @param Store $session
     * @param ServerRequestConnector $connector
     * @param LocalizerInterface $localizer
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException
     * @throws \Subscribo\Exception\Exceptions\RuntimeException
     */
    public function postQuestionary(Request $request, Store $session, ServerRequestConnector $connector, LocalizerInterface $localizer)
    {
        /** @var Questionary $questionary */
        $questionary = $session->get($this->sessionKeyQuestionary);
        if (empty($questionary)) {
            throw new SessionVariableNotFoundHttpException;
        }
        $session->keep([$this->sessionKeyQuestionary, $this->sessionKeyRedirectFromQuestionary]);
        $rules = $questionary->getQuestionsValidationRules();
        $this->validate($request, $rules);
        $data = $request->only(array_keys($rules));

        try {
            $response = $connector->postAnswer($questionary, $data, true);
        } catch (ServerRequestException $e) {
            $redirectUrl = $session->pull($this->sessionKeyRedirectFromQuestionary);
            $questionary = $session->pull($this->sessionKeyQuestionary);
            return $this->handleServerRequestException($e, $redirectUrl);
        } catch (ValidationErrorsException $e) {
            return redirect()
                ->refresh()
                ->withInput($data)
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            $this->logException($e);
            $errorMessage = $localizer->trans('traits.questionary.postQuestionary.errors.fallback', [], 'apiclientcommon::messages');
            return redirect()
                ->refresh()
                ->withInput($data)
                ->withErrors([$errorMessage]);
        }
        $redirectUrl = $session->pull($this->sessionKeyRedirectFromQuestionary);
        $questionary = $session->pull($this->sessionKeyQuestionary);
        if ($redirectUrl) {
            return redirect($redirectUrl)->with($this->sessionKeyServerRequestHandledResult, $response);
        }
        $redirectFromRequest = $request->query('redirect_back');
        if ($redirectFromRequest) {
            return redirect($redirectFromRequest);
        }
        throw new RuntimeException('Do not know where to redirect');
    }
}
