<?php namespace Subscribo\ApiClientCommon\Traits;

use Exception;
use RuntimeException;
use Subscribo\ApiClientCommon\Traits\RedirectToQuestionaryTrait;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\Questionary;
use Subscribo\Exception\Interfaces\ContainDataInterface;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Factories\MarkableExceptionFactory;

trait HandleServerRequestExceptionTrait
{
    use RedirectToQuestionaryTrait;

    protected function handleServerRequestException(ServerRequestException $exception, $backUri)
    {
        $serverRequest = $exception->getServerRequest();
        if ($serverRequest instanceof Questionary) {
            return $this->redirectToQuestionary($serverRequest, $backUri);
        }
        throw new RuntimeException (sprintf("Do not know how to handle this ServerRequest type '%s'", get_class($serverRequest)));
    }

    protected function logException(Exception $e)
    {
        $context = ['exception' => $e];
        if ($e instanceof MarkableExceptionInterface) {
            MarkableExceptionFactory::mark($e);
            $context['exceptionHash'] = $e->useMark();
        }
        if ($e instanceof ContainDataInterface) {
            $context['exceptionData'] = $e->getData();
        }
        \Log::error($e, $context);
    }
}
