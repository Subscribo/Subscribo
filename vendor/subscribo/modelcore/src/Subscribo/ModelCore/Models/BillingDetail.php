<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Address;

/**
 * Model BillingDetail
 *
 * Model class for being changed and used in the application
 */
class BillingDetail extends \Subscribo\ModelCore\Bases\BillingDetail
{
    const TYPE_ADDRESS_ONLY = 1;

    /**
     * @param int|Address $address
     * @return BillingDetail
     * @todo remove or refactor
     * @deprecated
     */
    public static function generate($address)
    {
        $instance = new static();
        $instance->address()->associate($address);
        $instance->type = static::TYPE_ADDRESS_ONLY;
        $instance->save();

        return $instance;
    }

    /**
     * @param Address $address
     * @param BillingDetail|null $billingDetail
     * @return BillingDetail
     * @todo remove or refactor
     * @deprecated
     */
    public static function addAddressOrGenerate(Address $address, BillingDetail $billingDetail = null)
    {
        return $billingDetail ? $billingDetail->addAddress($address) : static::generate($address);
    }

    /**
     * @param int|Address $address
     * @return $this
     * @todo remove or refactor
     * @deprecated
     */
    public function addAddress($address)
    {
        $this->address()->associate($address);
        $this->save();

        return $this;
    }
}
