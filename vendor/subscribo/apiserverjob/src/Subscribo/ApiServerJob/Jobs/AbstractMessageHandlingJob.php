<?php

namespace Subscribo\ApiServerJob\Jobs;

use RuntimeException;
use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ApiServerJob\Traits\EmailSendingTrait;
use Subscribo\ModelCore\Models\Message;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Interfaces\LocaleManagerInterface;
use Illuminate\Contracts\Mail\Mailer;
use Psr\Log\LoggerInterface;

/**
 * Abstract class AbstractMessageHandlingJob
 *
 * @package Subscribo\ApiServerJob
 */
abstract class AbstractMessageHandlingJob extends AbstractJob
{
    use EmailSendingTrait;

    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var LocaleManagerInterface  */
    protected $localeManager;

    /** @var LocalizerInterface */
    protected $localizer;

    /**
     * @return Message|null
     */
    abstract protected function getMessageModel();

    /**
     * @param Mailer $mailer
     * @return void
     */
    abstract protected function handleEmailMessage(Mailer $mailer);

    /**
     * @param Mailer $mailer
     * @param LocalizerInterface $localizer
     * @param LoggerInterface $logger
     * @param LocaleManagerInterface $localeManager
     * @throws \RuntimeException
     */
    public function handle(
        Mailer $mailer,
        LocalizerInterface $localizer,
        LoggerInterface $logger,
        LocaleManagerInterface $localeManager
    ) {
        $this->logger = $logger;
        $this->localeManager = $localeManager;
        $this->localizer = $localizer;
        $acquiredLocale = $this->acquireLocale();
        if (false !== $acquiredLocale) {
            $this->setLocale($acquiredLocale);
        }
        $message = $this->getMessageModel();
        if (empty($message)) {
            throw new RuntimeException('Message is empty');
        }
        switch (strval($message->type)) {
            case Message::TYPE_EMAIL:
                $this->handleEmailMessage($mailer);
                break;
            default:
                throw new RuntimeException('Do not know how to handle this message type');

        }
    }

    /**
     * To be overridden  in subclasses to actually return users locale
     * @return bool|string
     */
    protected function acquireLocale()
    {
        return false;
    }

    /**
     * @param $locale
     */
    protected function setLocale($locale)
    {
        $this->localeManager->setLocale($locale);
        if ( ! $this->localizer->haveTransparentLocale()) {
            $this->localizer->setLocale($locale);
        }
    }

    /**
     * @return LocalizerInterface
     */
    protected function getLocalizer()
    {
        return $this->localizer;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param string $message
     * @param array $context
     * @return null
     */
    protected function logInfo($message, array $context = [])
    {
        return $this->logger->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return null
     */
    protected function logNotice($message, array $context = [])
    {
        return $this->logger->notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return null
     */
    protected function logError($message, array $context = [])
    {
        return $this->logger->error($message, $context);
    }
}
