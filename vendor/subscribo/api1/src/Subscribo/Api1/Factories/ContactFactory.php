<?php

namespace Subscribo\Api1\Factories;

use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Customer;


class ContactFactory
{
    protected static $addressValidationRules = [
        'gender' => 'in:man,woman',
        'first_name' => 'max:100',
        'last_name' => 'required_with:city|max:100',
        'street' => 'required_with:city|max:255',
        'post_code' => 'max:30',
        'city' => 'max:100',
        'country' => 'required_with:city|max:100',
        'delivery_information' => 'max:500',
        'phone' => 'max:30',
        'mobile' => 'max:30',
    ];


    public static function getValidationRules($prefix = '')
    {
        if (empty($prefix)) {

            return self::$addressValidationRules;
        }
        $result = [];
        foreach (self::$addressValidationRules as $key => $value) {
            $prefixedKey = $prefix.$key;
            $prefixedValue = strtr($value, ['city' => ($prefix.'city')]);
            $result[$prefixedKey] = $prefixedValue;
        }

        return $result;
    }


    public static function dataContainAddress(array $data, $prefix = '')
    {
        return Address::dataContainsAddress(static::getPrefixed($data, $prefix));
    }


    public static function generateAddress(array $data, $prefix = '', Customer $customer = null)
    {
        return Address::generate(static::getPrefixed($data, $prefix), null, $customer);
    }


    public static function makeAddress(array $data, $prefix = '', Customer $customer = null)
    {
        return Address::make(static::getPrefixed($data, $prefix), null, $customer);
    }


    public static function findOrGenerate(array $data, $prefixes = '', Customer $customer = null)
    {
        $prefixes = (array) $prefixes;
        foreach ($prefixes as $prefix) {
            $processedData = static::getPrefixed($data, $prefix);
            $address = Address::ifDataContainsAddressFindSimilarOrGenerate($processedData, $customer);
            if ($address) {

                return $address;
            }
        }

        return null;
    }

    protected static function customerHaveSimilarContact(Customer $customer, $data)
    {
        foreach ($customer->contacts as $contact)
        {

        }
    }

    protected static function dataContainsShippingAddress($data)
    {
        $prefixed = static::getPrefixed($data, 'shipping_');

        return Address::dataContainsAddress($prefixed);
    }

    protected static function dataContainsBillingAddress($data)
    {
        $prefixed = static::getPrefixed($data, 'billing_');

        return Address::dataContainsAddress($prefixed);
    }

    /**
     * @param array $data
     * @param string $prefix
     * @return array
     */
    protected static function getPrefixed(array $data, $prefix = '')
    {
        if (empty($prefix)) {

            return $data;
        }

        $result = [];
        $prefixLength = (strlen($prefix));
        foreach($data as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $simplifiedKey = substr($key, $prefixLength);
                $result[$simplifiedKey] = $value;
            }
        }

        return $result;
    }
}
