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
     */
    public static function generate($address)
    {
        $addressId = ($address instanceof Address) ? $address->id : intval($address);
        $instance = new static();
        $instance->addressId = $addressId;
        $instance->type = static::TYPE_ADDRESS_ONLY;
        $instance->save();

        return $instance;
    }

    /**
     * @param Address $address
     * @param BillingDetail|null $billingDetail
     * @return BillingDetail
     */
    public static function addAddressOrGenerate(Address $address, BillingDetail $billingDetail = null)
    {
        return $billingDetail ? $billingDetail->addAddress($address) : static::generate($address);
    }

    /**
     * @param int|Address $address
     * @return $this
     */
    public function addAddress($address)
    {
        $addressId = ($address instanceof Address) ? $address->id : intval($address);
        $this->addressId = $addressId;
        $this->save();

        return $this;
    }
}
