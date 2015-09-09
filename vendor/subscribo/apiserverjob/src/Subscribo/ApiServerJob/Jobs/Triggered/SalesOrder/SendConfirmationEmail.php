<?php

namespace Subscribo\ApiServerJob\Jobs\Triggered\SalesOrder;

use Subscribo\ApiServerJob\Jobs\AbstractMessageHandlingJob;
use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class SendConfirmationEmail
 *
 * @package Subscribo\ApiServerJob
 */
class SendConfirmationEmail extends AbstractMessageHandlingJob
{
    /** @var \Subscribo\ModelCore\Models\SalesOrder  */
    protected $salesOrder;

    /**
     * @param SalesOrder $salesOrder
     */
    public function __construct(SalesOrder $salesOrder)
    {
        $this->salesOrder = $salesOrder;
    }

    /**
     * @return null|\Subscribo\ModelCore\Models\Message
     */
    protected function getMessageModel()
    {
        return $this->salesOrder->confirmationMessage;
    }

    /**
     * @param Mailer $mailer
     * @param LocalizerInterface $localizer
     */
    protected function handleEmailMessage(Mailer $mailer, LocalizerInterface $localizer)
    {
        $message = $this->getMessageModel();
        $loc = $localizer->template('emails', 'apiserverjob')->setPrefix('order.manual.confirm');
        if (empty($message->subject)) {
            $message->subject = $loc->transOrDefault('subject');
        }
        $templatePath = $loc->transOrDefault('templatePath');
        $viewData = [
            'person' => $this->salesOrder->account->customer->person,
            'salesOrder' => $this->salesOrder,
        ];
        $emailSent = $this->sendEmail($mailer, $message, $templatePath, $viewData);
        if ($emailSent) {
            $this->logger->notice("Confirmation email for sales order with hash: '".$this->salesOrder->hash."' sent.");
        } else {
            $this->logger->error("Attempt to send confirmation email for sales order with hash: '"
                                .$this->salesOrder->hash."' has failed (at least for one of addresses).");
        }
    }
}
