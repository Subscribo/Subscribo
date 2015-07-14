<?php

namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractBusinessController;
use Subscribo\ModelCore\Models\Product;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;

/**
 * Class BusinessController
 *
 * @package Subscribo\Api1
 */
class BusinessController extends AbstractBusinessController
{
    public function actionGetProduct($id = null)
    {
        $serviceId = $this->context->getServiceId();
        $countryId = $this->acquireCountryId();
        $currencyId = $this->acquireCurrencyId($countryId);

        if (is_null($id)) {

            return ['result' => Product::findAllByServiceIdWithPrices($serviceId, $currencyId, $countryId)];
        }
        if (is_numeric($id)) {
            $product = Product::withTranslations()->find($id);
        } else {
            $product = Product::withTranslations()
                ->where('identifier', $id)
                ->where('service_id', $serviceId)
                ->first();
        }
        if (empty($product)) {
            throw new InstanceNotFoundHttpException();
        }
        if ($product->serviceId !== $serviceId) {
            throw new WrongServiceHttpException();
        }

        return ['result' => $product->toArrayWithPrices($currencyId, $countryId)];
    }
}
