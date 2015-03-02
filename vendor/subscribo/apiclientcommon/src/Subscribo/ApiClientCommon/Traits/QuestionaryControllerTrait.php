<?php namespace Subscribo\ApiClientCommon\Traits;

use Exception;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\AccountIdTransport;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestHttpExceptionTrait;
use Subscribo\ApiClientCommon\Connectors\QuestionaryConnector;


trait QuestionaryControllerTrait
{
    use HandleServerRequestHttpExceptionTrait;
    use ValidatesRequests;

    public function getQuestionary(Store $session)
    {
        $questionary = $session->get($this->sessionKeyQuestionary);
        if (empty($questionary)) {
            throw new SessionVariableNotFoundHttpException;
        }
        $session->reflash();
        return view('subscribo::apiclientcommon.questionary')->with('questionary', $questionary);
    }

    public function postQuestionary(Request $request, Store $session, QuestionaryConnector $connector, Guard $auth)
    {
        /** @var Questionary $questionary */
        $questionary = $session->get($this->sessionKeyQuestionary);
        if (empty($questionary)) {
            throw new SessionVariableNotFoundHttpException;
        }
        $session->reflash();
        $rules = $questionary->getQuestionsValidationRules();
        $this->validate($request, $rules);
        $data = $request->only(array_keys($rules));

        $user = $auth->user();
        $signatureOptions = $user ? AccountIdTransport::setAccountId($user->getAuthIdentifier()) : array();
        try {
            $response = $connector->postAnswer($questionary, $data, $signatureOptions);
        } catch (ServerRequestHttpException $e) {
            $redirectUri = $session->pull($this->sessionKeyRedirectFromQuestionary);
            $questionary = $session->pull($this->sessionKeyQuestionary);
            return $this->handleServerRequestHttpException($e, $redirectUri);
        } catch (ValidationErrorsException $e) {
            return redirect()
                ->refresh()
                ->withInput($data)
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            return redirect()
                ->refresh()
                ->withInput($data)
                ->withErrors(['Some error has happened. Please try again later or contact an administrator.']);
        }
        $redirectUri = $session->pull($this->sessionKeyRedirectFromQuestionary);
        $questionary = $session->pull($this->sessionKeyQuestionary);
        return redirect($redirectUri)->with($this->sessionKeyQuestionaryAnswerResult, $response);
    }

}
