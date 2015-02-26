<?php namespace Subscribo\ApiClientCommon\Traits;


use Subscribo\ApiClientCommon\Traits\RedirectToQuestionaryTrait;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\RestCommon\Questionary;
use RuntimeException;

trait HandleServerRequestHttpExceptionTrait
{
    use RedirectToQuestionaryTrait;

    protected function handleServerRequestHttpException(ServerRequestHttpException $exception, $backUri)
    {
        $serverRequest = $exception->getServerRequest();
        if ($serverRequest instanceof Questionary) {
            return $this->redirectToQuestionary($serverRequest, $backUri);
        }
        throw new RuntimeException (sprintf("Do not know how to handle this ServerRequest type '%s'", get_class($serverRequest)));
    }

}