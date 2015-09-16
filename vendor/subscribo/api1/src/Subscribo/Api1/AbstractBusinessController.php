<?php

namespace Subscribo\Api1;

use Subscribo\Api1\AbstractController;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;
use Subscribo\ModelCore\Models\Country;
use Subscribo\ModelCore\Models\Currency;

abstract class AbstractBusinessController extends AbstractController
{
    /**
     * @return int|null
     * @throws \Subscribo\Exception\Exceptions\InvalidQueryHttpException
     */
    protected function acquireCountryId()
    {
        $countryIdentifier = $this->context->getRequest()->query->get('country');
        if (empty($countryIdentifier)) {

            return $this->context->getService()->defaultCountryId;
        }
        $country = Country::findByIdentifier($countryIdentifier);
        if ($country) {

            return $country->id;

        }
        throw new InvalidQueryHttpException('Specified country is invalid');
    }

    /**
     * @param int$countryId
     * @return int|null
     * @throws \Subscribo\Exception\Exceptions\InvalidQueryHttpException
     */
    protected function acquireCurrencyId($countryId)
    {
        $currencyIdentifier = $this->context->getRequest()->query->get('currency');

        if ($currencyIdentifier) {
            $currency = Currency::findByIdentifier($currencyIdentifier);
            if ($currency) {

                return $currency->id;
            }

            throw new InvalidQueryHttpException('Specified currency is invalid');
        }
        $currencyId = $this->context->getService()->provideDefaultCurrencyId($countryId);
        if ($currencyId) {

            return $currencyId;
        }
        $exceptionMessage = $countryId
            ? 'Currency not specified for country in use'
            : 'Currency and country not specified neither predefined';

        throw new InvalidQueryHttpException($exceptionMessage);
    }
}
