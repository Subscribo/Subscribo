<?php

namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractBusinessController;
use Subscribo\ModelCore\Models\PaymentMethod;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;

/**
 * Class PaymentController
 *
 * @package Subscribo\Api1
 */
class PaymentController extends AbstractBusinessController
{
    public function actionGetMethod($id = null)
    {
        $serviceId = $this->context->getServiceId();
        $countryId = $this->acquireCountryId();
        $currencyId = $this->acquireCurrencyId($countryId);

        if (is_null($id)) {

            return ['result' => PaymentMethod::findAvailable($serviceId, $countryId, $currencyId)];
        }
        $paymentMethod = PaymentMethod::findByIdentifier($id);

        if (empty($paymentMethod)) {
            throw new InstanceNotFoundHttpException();
        }

        return ['result' => $paymentMethod];
    }
}
