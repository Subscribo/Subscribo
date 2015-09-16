<?php

namespace Subscribo\ApiServerJob\Jobs\Triggered\Transaction;

use RuntimeException;
use Subscribo\ApiServerJob\Jobs\AbstractMessageHandlingJob;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class SendConfirmationMessage
 *
 * @package Subscribo\ApiServerJob
 */
class SendConfirmationMessage extends AbstractMessageHandlingJob
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
     * @return string
     */
    protected function acquireLocale()
    {
        return $this->transaction->account->locale;
    }

    /**
     * @param Mailer $mailer
     * @throws \RuntimeException
     */
    protected function handleEmailMessage(Mailer $mailer)
    {
        $message = $this->getMessageModel();
        $idBase = 'transaction.';
        $idBase .= (Transaction::METHOD_INVOICE === $this->transaction->method) ? 'invoice.' : 'atomic.';
        $idBase .= (Transaction::ORIGIN_SYSTEM === $this->transaction->origin) ? 'system.' : 'manual.';
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
        $localizer = $this->getLocalizer()->duplicate('emails', 'apiserverjob');
        if (empty($message->subject)) {
            $message->subject = $localizer->transOrDefault($idBase.'subject'.$idEnd);
        }
        $content = $localizer->transOrDefault($idBase.'content'.$idEnd);
        $detailData = [
            'transaction' => $this->transaction,
            'currencySymbol' => $this->transaction->currency->symbol,
            'gatewayName' => $this->transaction->transactionGatewayConfiguration->transactionGateway->name,
            'processingDate' => $this->transaction->lastRequestSentOn,
        ];
        $detail = view($localizer->transOrDefault('transaction.detail.templatePath'), $detailData);
        $paragraphs = [$content, $detail];
        $heading = $localizer->transOrDefault('generic.heading', ['%salutation%' => $salutation]);
        $ending = $localizer->transOrDefault('generic.ending');
        $viewData = [
            'heading' => $heading,
            'paragraphs' => $paragraphs,
            'ending' => $ending,
        ];
        $templatePath = 'subscribo::apiserverjob.emails.generic';
        $emailSent = $this->sendEmail($mailer, $message, $templatePath, $viewData);
        if ($emailSent) {
            $this->logNotice("Confirmation email for transaction with hash: '".$this->transaction->hash."' sent.");
        } else {
            $this->logError("Attempt to send confirmation email for transaction with hash: '"
                            .$this->transaction->hash."' has failed (at least for one of addresses).");
        }
    }
}
