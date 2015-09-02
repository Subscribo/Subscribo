<?php

namespace Subscribo\ApiServerCommon\Jobs;

use RuntimeException;
use Subscribo\ApiServerCommon\Jobs\AbstractJob;
use Subscribo\ModelCore\Models\Message;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\Mail\Mailer;
use Subscribo\ApiServerCommon\Traits\EmailSendingTrait;

/**
 * Abstract class AbstractMessageHandlingJob
 *
 * @package Subscribo\ApiServerCommon
 */
abstract class AbstractMessageHandlingJob extends AbstractJob
{
    use EmailSendingTrait;

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
     * @throws \RuntimeException
     */
    public function handle(Mailer $mailer, LocalizerInterface $localizer)
    {
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
