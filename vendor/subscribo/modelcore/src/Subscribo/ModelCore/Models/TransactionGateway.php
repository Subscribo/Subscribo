<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelBase\Traits\SearchableByIdentifierTrait;
use Subscribo\ModelCore\Models\TransactionGatewayConfiguration;

/**
 * Model TransactionGateway
 *
 * Model class for being changed and used in the application
 */
class TransactionGateway extends \Subscribo\ModelCore\Bases\TransactionGateway
{
    use SearchableByIdentifierTrait;

    public static function findAvailable($serviceId, $countryId = null, $currencyId = null)
    {
        $configurations = TransactionGatewayConfiguration::getCollectionByAttributes($serviceId, $countryId, $currencyId, null, true, true);
        $result = [];
        foreach ($configurations as $configuration) {
            $item = $configuration->transactionGateway->toArray();
            $item['is_default'] = $configuration->isDefault;
            $result[] = $item;
        }

        return $result;
    }
}
