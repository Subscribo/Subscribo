<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelBase\Traits\SearchableByIdentifierTrait;
use Subscribo\ModelCore\Models\PaymentConfiguration;

/**
 * Model PaymentMethod
 *
 * Model class for being changed and used in the application
 */
class PaymentMethod extends \Subscribo\ModelCore\Bases\PaymentMethod
{
    use SearchableByIdentifierTrait;

    public static function findAvailable($serviceId, $countryId = null, $currencyId = null)
    {
        $configurations = PaymentConfiguration::findByAttributes($serviceId, $countryId, $currencyId, true);
        $result = [];
        foreach ($configurations as $configuration) {
            $item = $configuration->paymentMethod->toArray();
            $item['is_default'] = $configuration->isDefault;
            $result[] = $item;
        }

        return $result;
    }

}
