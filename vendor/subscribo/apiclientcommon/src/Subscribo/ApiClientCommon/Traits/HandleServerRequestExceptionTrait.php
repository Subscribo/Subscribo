<?php namespace Subscribo\ApiClientCommon\Traits;

use Exception;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestCommon\Widget;
use Subscribo\Exception\Interfaces\ContainDataInterface;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Factories\MarkableExceptionFactory;
use Subscribo\Exception\Exceptions\RuntimeException;
use Psr\Log\LoggerInterface;

/**
 * Trait HandleServerRequestExceptionTrait
 *
 * @package Subscribo\ApiClientCommon
 */
trait HandleServerRequestExceptionTrait
{
    protected $sessionKeyQuestionaryServerRequest = 'subscribo_apiclientcommon_questionary_object';
    protected $sessionKeyRedirectFromQuestionary = 'subscribo_apiclientcommon_redirect_from_questionary';
    protected $sessionKeyServerRequestHandledResult = 'subscribo_apiclientcommon_server_request_handled_result';
    protected $sessionKeyClientRedirection = 'subscribo_apiclientcommon_client_redirection_object';
    protected $sessionKeyRedirectFromClientRedirection = 'subscribo_apiclientcommon_redirect_from_client_redirection';
    protected $sessionKeyWidgetServerRequest = 'subscribo_apiclientcommon_widget_object';
    protected $sessionKeyRedirectFromWidget = 'subscribo_apiclientcommon_redirect_from_widget';

    /**
     * @param ServerRequestException $exception
     * @param string $backUri
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Subscribo\Exception\Exceptions\RuntimeException
     */
    protected function handleServerRequestException(ServerRequestException $exception, $backUri)
    {
        $serverRequest = $exception->getServerRequest();
        if ($serverRequest instanceof Questionary) {
            return $this->handleQuestionaryServerRequest($serverRequest, $backUri);
        }
        if ($serverRequest instanceof ClientRedirection) {
            return $this->handleClientRedirectionServerRequest($serverRequest, $backUri);
        }
        if ($serverRequest instanceof Widget) {
            return $this->handleWidgetServerRequest($serverRequest, $backUri);
        }
        throw new RuntimeException (sprintf("Do not know how to handle this ServerRequest type '%s'", get_class($serverRequest)));
    }

    /**
     * @param Questionary $questionary
     * @param string $backUri
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleQuestionaryServerRequest(Questionary $questionary, $backUri)
    {
        return redirect()->route('subscribo.serverRequest.questionary')
            ->with($this->sessionKeyQuestionaryServerRequest, $questionary)
            ->with($this->sessionKeyRedirectFromQuestionary, $backUri);
    }

    /**
     * @param ClientRedirection $clientRedirection
     * @param string $backUri
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Subscribo\Exception\Exceptions\RuntimeException
     */
    protected function handleClientRedirectionServerRequest(ClientRedirection $clientRedirection, $backUri)
    {
        $parameters = $clientRedirection->hash ? ['hash' => $clientRedirection->hash] : array();
        $redirectBackHandlerUrl = redirect()->getUrlGenerator()->route('subscribo.serverRequest.clientRedirect', $parameters);
        $url = $clientRedirection->getUrl($redirectBackHandlerUrl);
        if (empty($url)) {
            throw new RuntimeException('handleClientRedirectionServerRequest(): Empty url');
        }
        if ($clientRedirection->remember) {
            return redirect($url)
                ->with($this->sessionKeyClientRedirection, $clientRedirection)
                ->with($this->sessionKeyRedirectFromClientRedirection, $backUri);
        } else {
            return redirect($url);
        }
    }

    /**
     * @param Widget $widget
     * @param string $backUri
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleWidgetServerRequest(Widget $widget, $backUri)
    {
        return redirect()->route('subscribo.serverRequest.widget.display')
            ->with($this->sessionKeyWidgetServerRequest , $widget)
            ->with($this->sessionKeyRedirectFromWidget, $backUri);
    }

    /**
     * @param Exception $e
     * @param LoggerInterface|null $logger
     */
    protected function logException(Exception $e, LoggerInterface $logger = null)
    {
        $context = ['exception' => $e];
        if ($e instanceof MarkableExceptionInterface) {
            MarkableExceptionFactory::mark($e);
            $context['exceptionHash'] = $e->useMark();
        }
        if ($e instanceof ContainDataInterface) {
            $context['exceptionData'] = $e->getData();
        }
        if ($logger) {
            $logger->error($e->__toString(), $context);
        } else {
            \Log::error($e, $context);
        }
    }
}
