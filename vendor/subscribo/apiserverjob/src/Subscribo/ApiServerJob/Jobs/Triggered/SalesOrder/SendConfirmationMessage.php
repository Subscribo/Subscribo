<?php

namespace Subscribo\ApiServerJob\Jobs\Triggered\SalesOrder;

use Subscribo\ApiServerJob\Jobs\AbstractMessageHandlingJob;
use Subscribo\ModelCore\Models\SalesOrder;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class SendConfirmationMessage
 *
 * @package Subscribo\ApiServerJob
 */
class SendConfirmationMessage extends AbstractMessageHandlingJob
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
     * @return string
     */
    protected function acquireLocale()
    {
        return $this->salesOrder->account->locale;
    }

    /**
     * @param Mailer $mailer
     */
    protected function handleEmailMessage(Mailer $mailer)
    {
        $type = $this->salesOrder->type ?: SalesOrder::TYPE_MANUAL;
        $localizer = $this->getLocalizer()->template('emails', 'apiserverjob')->setPrefix('order.'.$type.'.confirm');
        $message = $this->getMessageModel();
        if (empty($message->subject)) {
            $message->subject = $localizer->transOrDefault('subject');
        }
        $templatePath = $localizer->transOrDefault('templatePath');
        $person = $this->salesOrder->account->customer->person;
        $salutation = $person->salutation ?: $person->name;
        $items = [];
        $currency = $this->salesOrder->currency;
        $countryId = $this->salesOrder->countryId;
        foreach ($this->salesOrder->realizationsInSalesOrders as $realizationInSalesOrder) {
            $price = $realizationInSalesOrder->price;
            $product = $price->product;
            $item = $product->toArrayWithPriceAndAmount($price, $countryId, $realizationInSalesOrder->amount);
            $realization = $realizationInSalesOrder->realization;
            if ($realization->name) {
                $item['name'] = $realization->name;
            }
            if ($realization->description) {
                $item['description'] = $realization->description;
            }
            $items[] = $item;
        }
        $viewData = [
            'salutation' => $salutation,
            'person' => $person,
            'salesOrder' => $this->salesOrder,
            'currency' => $currency,
            'currencySymbol' => $currency->symbol,
            'items' => $items,
            'discounts' => $this->salesOrder->discounts,
            'totalNetSum' => $this->salesOrder->netSum,
            'totalGrossSum' => $this->salesOrder->grossSum,
        ];
        $emailSent = $this->sendEmail($mailer, $message, $templatePath, $viewData);
        if ($emailSent) {
            $this->logNotice("Confirmation email for sales order with hash: '".$this->salesOrder->hash."' sent.");
        } else {
            $this->logError("Attempt to send confirmation email for sales order with hash: '"
                                .$this->salesOrder->hash."' has failed (at least for one of addresses).");
        }
    }
}
