<?php

namespace Subscribo\TransactionPluginManager;

use RuntimeException;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\ServiceModule;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Omnipay\Shared\CreditCard;
use Subscribo\Omnipay\Shared\ItemBag as ShoppingCart;
use Subscribo\Omnipay\Shared\Item as ShoppingCartItem;

/**
 * Class Utils - various static methods
 *
 * @package Subscribo\TransactionPluginManager
 */
class Utils
{
    protected static function assembleWidgetReturnUrl(Service $service, $hash)
    {
        $parameters = ['hash' => $hash];
        $url = ServiceModule::retrieveUri($service, ServiceModule::MODULE_WIDGET, $parameters);
        if (0 === strpos($url, '/')) {
            if (empty($service->url)) {
                throw new RuntimeException('Provided service does not have url defined');
            }
            $url = ($service->url).$url;
        }
        return $url;
    }

    protected static function limitStringLength($input, $limit = 120, $delimiter = ' ', $ending = '...')
    {
        if ($delimiter) {
            $parts = explode($delimiter, $input);
        } else {
            $parts = mb_split('/./', $input);
        }
        $result = '';
        $first = true;
        foreach ($parts as $part) {
            if ((strlen($result) + strlen($part) + 1) > $limit) {

                return $result.$ending;
            }
            if ($first) {
                $first = false;
            } else {
                $result .= ' ';
            }
            $result .= $part;
        }

        return $result;
    }

    protected static function assembleTransactionDescription(SalesOrder $salesOrder, LocalizerInterface $localizer)
    {
        $description = $localizer->trans('transaction.description.intro');
        $first = true;
        foreach ($salesOrder->realizationsInSalesOrders as $realization)
        {
            if ($first) {
                $first = false;
            } else {
                $description .= ',';
            }
            $description .= ' '.$realization->price->product->name;
            if (strval($realization->amount) !== '1') {
                $description .= ' x '.$realization->amount;
            }
        }
        return $description;
    }

    protected static function assembleCardData(Address $billingAddress = null, Address $shippingAddress = null)
    {
        if (empty($billingAddress) and empty($shippingAddress)) {

            return null;
        }
        $billingAddress = $billingAddress ?: $shippingAddress;
        $shippingAddress = $shippingAddress ?: $billingAddress;
        $data = static::assembleAddressData($billingAddress) + static::assembleAddressData($shippingAddress, false);

        return $data;
    }

    /**
     * @param Address $address
     * @param bool $billing
     * @param Person $person
     * @return array
     */
    protected static function assembleAddressData(Address $address, $billing = true, Person $person = null)
    {
        $prefix = $billing ? 'billing' : 'shipping';
        $person = $person ?: $address->person;
        $data = [
            'firstName' => $person->firstName.($person->middleNames ? ' '.$person->middleNames : ''),
            'lastName' => $person->lastName,
            'title' => $person->prefix,
            'company' => $address->companyName,
            'address1' => $address->compileStreetLine(),
            'city' => $address->city,
            'postcode' => $address->postCode,
            'state' => $address->state ? $address->state->identifier : null,
            'country' => $address->country->identifier,
            'phone' => $address->phone,
            'mobile' => $address->mobile,
        ];
        $result = [];
        foreach ($data as $key => $value) {
            if ($value) {
                $resultKey = $prefix ? ($prefix.ucfirst($key)) : $key;
                $result[$resultKey] = $value;
            }
        }
        if ($billing and (Person::GENDER_MAN === $person->gender)) {
            $result['gender'] = CreditCard::GENDER_MALE;
        }
        if ($billing and (Person::GENDER_WOMAN === $person->gender)) {
            $result['gender'] = CreditCard::GENDER_FEMALE;
        }
        if ($billing and $person->dateOfBirth) {
            $result['birthday'] = $person->dateOfBirth;
        }

        return $result;
    }

    /**
     * @param \Subscribo\ModelCore\Models\RealizationsInSalesOrder[] $realizationsInSalesOrders
     * @param \Subscribo\ModelCore\Models\Discount[] $discounts
     * @param $country
     * @return ShoppingCart
     */
    protected static function assembleShoppingCart($realizationsInSalesOrders, $discounts = [], $country)
    {
        $result = new ShoppingCart();
        /** @var \Subscribo\ModelCore\Models\RealizationsInSalesOrder $realizationInSalesOrder */
        foreach ($realizationsInSalesOrders as $realizationInSalesOrder) {
            $price = $realizationInSalesOrder->price;
            $product = $price->product;
            $priceData = $product->toArrayWithPrice($price, $country);
            $item = new ShoppingCartItem();
            $item->setName($product->name);
            $item->setDescription($product->description);
            $item->setIdentifier($product->identifier);
            $item->setTaxPercent($priceData['tax_percent']);
            $item->setPrice($priceData['price_gross']);
            $item->setQuantity($realizationInSalesOrder->amount);
            $result->add($item);
        }
        //todo implement discounts handling

        return $result;
    }
}
