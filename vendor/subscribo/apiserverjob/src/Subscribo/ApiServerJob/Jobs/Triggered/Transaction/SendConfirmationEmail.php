<?php

namespace Subscribo\ApiServerJob\Jobs\Triggered\Transaction;

use RuntimeException;
use Subscribo\ApiServerJob\Jobs\AbstractMessageHandlingJob;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class SendConfirmationEmail
 *
 * @package Subscribo\ApiServerJob
 */
class SendConfirmationEmail extends AbstractMessageHandlingJob
{
    /** @var \Subscribo\ModelCore\Models\Transaction  */
    protected $transaction;

    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return null|\Subscribo\ModelCore\Models\Message
     */
    protected function getMessageModel()
    {
        return $this->transaction->confirmationMessage;
    }

    /**
     * @param Mailer $mailer
     * @param LocalizerInterface $localizer
     * @throws \RuntimeException
     */
    protected function handleEmailMessage(Mailer $mailer, LocalizerInterface $localizer)
    {
        $message = $this->getMessageModel();
        $idBase = 'transaction.';
        $idBase .= (Transaction::METHOD_INVOICE === $this->transaction->method) ? 'invoice' : 'atomic';
        $idBase .= '.manual.';
        $result = $this->transaction->result;
        if (empty($result)) {
            throw new RuntimeException('This transaction is not finished yet');
        }
        $idEnd = '.'.$result;
        if (Transaction::RESULT_WAITING === $result) {
            if (Transaction::STATUS_WAITING_FOR_CUSTOMER_INPUT === $this->transaction->status) {
                $idEnd .= '.customerInput';
            } elseif (Transaction::STATUS_WAITING_FOR_CUSTOMER_CONFIRMATION === $this->transaction->status) {
                $idEnd .= '.customerConfirmation';
            } else {
                return;
            }
        }
        $person = $this->transaction->account->customer->person;
        $salutation = $person->salutation ?: $person->name;
        $loc = $localizer->duplicate('emails', 'apiserverjob');
        if (empty($message->subject)) {
            $message->subject = $loc->transOrDefault($idBase.'subject'.$idEnd);
        }
        $content = $loc->transOrDefault($idBase.'content'.$idEnd);
        $heading = $loc->transOrDefault('generic.heading', ['%salutation%' => $salutation]);
        $ending = $loc->transOrDefault('generic.ending');
        $viewData = [
            'heading' => $heading,
            'content' => $content,
            'ending' => $ending,
        ];
        $templatePath = 'subscribo::apiserverjob.emails.generic';
        $this->sendEmail($mailer, $message, $templatePath, $viewData);
    }
}
