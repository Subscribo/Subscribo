<?php

namespace Subscribo\ApiServerJob\Jobs;

use RuntimeException;
use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ApiServerJob\Traits\EmailSendingTrait;
use Subscribo\ModelCore\Models\Message;
use Subscribo\Localization\Interfaces\LocalizerInterface;
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

    /**
     * @return Message|null
     */
    abstract protected function getMessageModel();

    /**
     * @param Mailer $mailer
     * @param LocalizerInterface $localizer
     * @return void
     */
    abstract protected function handleEmailMessage(Mailer $mailer, LocalizerInterface $localizer);

    /**
     * @param Mailer $mailer
     * @param LocalizerInterface $localizer
     * @param LoggerInterface $logger
     * @throws \RuntimeException
     */
    public function handle(Mailer $mailer, LocalizerInterface $localizer, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $message = $this->getMessageModel();
        if (empty($message)) {
            throw new RuntimeException('Message is empty');
        }
        switch (strval($message->type)) {
            case Message::TYPE_EMAIL:
                $this->handleEmailMessage($mailer, $localizer);
                break;
            default:
                throw new RuntimeException('Do not know how to handle this message type');

        }
    }
}
