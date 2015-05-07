<?php

namespace Omnipay\Klarna\Message;

use Klarna;
use KlarnaAddr;
use KlarnaCountry;
use KlarnaCurrency;
use KlarnaFlags;
use KlarnaLanguage;
use Omnipay\Klarna\Message\AbstractInvoiceRequest;
use Omnipay\Klarna\Message\InvoiceAuthorizeResponse;
use Omnipay\Klarna\Traits\InvoiceGatewayDefaultParametersGettersAndSettersTrait;
use Omnipay\Common\Exception\InvalidRequestException;
use Subscribo\Omnipay\Shared\CreditCard;


class InvoiceAuthorizeRequest extends AbstractInvoiceRequest
{
    use InvoiceGatewayDefaultParametersGettersAndSettersTrait;

    public function getData()
    {
        $this->validate('merchantId', 'sharedSecret', 'country', 'language', 'currency', 'card');
        $data = $this->getParameters();
        $data['amount'] = $this->getAmount() ?: -1;
        $card = $this->getCard();
        $country = strtoupper($this->getCountry());
        switch ($country) {
            case 'AT':
            case 'DE':
            case 'NL':
                $gender = $card->getGender();
                $pno = $card->getBirthday('dmY');
                if (empty($gender)) {
                    throw new InvalidRequestException('Gender is a required parameter for AT/DE/NL');
                }
                if (empty($pno)) {
                    throw new InvalidRequestException('Birthday is a required parameter for AT/DE/NL');
                }
                $data['gender'] = strtolower(substr($gender, 0, 1));
            break;
            default:
                $pno = $card->getSocialSecurityNumber();
                if (empty($pno)) {
                    throw new InvalidRequestException('SocialSecurityNumber is a required parameter for this country');
                }
                $data['gender'] = null;
        }
        $data['pno'] = $pno;
        $itemBag = $this->getItems();
        /** @var \Subscribo\Omnipay\Shared\Item $item */
        $items = $itemBag ? $itemBag->all() : [];
        $articles = [];
        foreach ($items as $item) {
            $article = [
                'quantity' => $item->getQuantity(),
                'artNo' => $item->getIdentifier(),
                'title' => $item->getName(),
                'price' => $item->getPrice(),
                'vat'   => $item->getTaxPercent() ?: 0,
                'discount' => $item->getDiscountPercent() ?: 0,
                'flags' => $item->getFlags(),
            ];
            $articles[] = $article;
        }
        $data['articles'] = $articles;
        return $data;
    }


    public function sendData($data)
    {
        if (( ! is_array($data))) {
            throw new \InvalidArgumentException('Data parameter should be an array');
        }
        $k = new Klarna();
        /** @var \Subscribo\Omnipay\Shared\CreditCard $card */
        $card = $data['card'];
        $billingAddress = $this->createKlarnaAddr($card);
        if ($card->getShippingContactDifferences()) {
            $shippingAddress = $this->createKlarnaAddr($card, true);
        } else {
            $shippingAddress = $billingAddress;
        }
        $country = KlarnaCountry::fromCode($data['country']);
        $language = KlarnaLanguage::fromCode($data['language']);
        $currency = KlarnaCurrency::fromCode($data['currency']);
        $mode = $data['testMode'] ? Klarna::BETA : Klarna::LIVE;
        $k->config(
            $data['merchantId'],
            $data['sharedSecret'],
            $country,
            $language,
            $currency,
            $mode
        );
        $k->setAddress(KlarnaFlags::IS_BILLING, $billingAddress);
        $k->setAddress(KlarnaFlags::IS_SHIPPING, $shippingAddress);
        foreach ($data['articles'] as $article) {
            $flags = isset($article['flags']) ? $article['flags'] : KlarnaFlags::INC_VAT;
            $k->addArticle(
                $article['quantity'],
                $article['artNo'],
                $article['title'],
                $article['price'],
                $article['vat'],
                $article['discount'],
                $flags
            );
        }
        $result = $k->reserveAmount($data['pno'], $data['gender'], $data['amount']);

        $this->response = $this->createResponse($result);
        return $this->response;
    }


    protected function createResponse(array $data)
    {
        return new InvoiceAuthorizeResponse($this, $data);
    }


    protected function createKlarnaAddr(CreditCard $card, $isShipping = false)
    {
        $phone = $isShipping ? $card->getShippingPhone() : $card->getPhone();
        $mobile = $isShipping ? $card->getShippingMobile() : $card->getMobile();
        $firstName = $isShipping ? $card->getShippingFirstName() : $card->getFirstName();
        $lastName = $isShipping ? $card->getShippingLastName() : $card->getLastName();
        $postCode = $isShipping ? $card->getShippingPostcode() : $card->getPostcode();
        $city = $isShipping ? $card->getShippingCity() : $card->getCity();
        $country = strtoupper($isShipping ? $card->getShippingCountry() : $card->getCountry());
        $address1 = $isShipping ? $card->getShippingAddress1() : $card->getAddress1();
        $address2 = $isShipping ? $card->getShippingAddress2() : $card->getAddress2();

        $careof = '';
        $street = $address1;
        $houseNo = null;
        $houseExt = null;
        if (('AT' === $country) or ('DE' === $country) or ('NL' === $country)) {
            if (is_null($address2)) {
                list($street, $houseNo) = $this->parseAddressLine($address1, ('NL' === $country));
            } else {
                $houseNo = $address2;
            }
        } else {
            if ($address2) {
                $street .= ' '.$address2;
            }
        }
        $result = new KlarnaAddr(
            $card->getEmail(),
            $phone,
            $mobile,
            $firstName,
            $lastName,
            $careof,
            $street,
            $postCode,
            $city,
            KlarnaCountry::fromCode($country),
            $houseNo,
            $houseExt
        );
        return $result;
    }


    protected function parseAddressLine($addressLine, $romanNumerals = false)
    {
        $parts = preg_split('/[\\s]+/', $addressLine, null, PREG_SPLIT_NO_EMPTY);
        if (2 === count($parts)) { // Most simple case
            return $parts;
        }
        if ($romanNumerals) {
            $houseNoPattern = '%^[ivxIVX0-9\\/\\-\\.\\|\\+\\#\\~\\(\\)\\[\\]\\{\\}\\<\\>\\,\\#]]+$%';
        } else {
            $houseNoPattern = '%^[0-9\\/\\-\\.\\|\\+\\#\\~\\(\\)\\[\\]\\{\\}\\<\\>\\,\\#]]+$%';
        }
        $street = array_shift($parts);
        $houseNo = '';
        while ($parts) {
            $last = end($parts);
            //If the last element consists only of numeric and punctuation, we consider it part of House Number
            if (preg_match($houseNoPattern, $last)) {
                $houseNo = array_pop($parts).$houseNo;
            } else {
                break;
            }
        }
        while ($parts) {
            $first = reset($parts);
            if (preg_match('/^[^0-9]+$/', $first)) {
                $street .= ' '.array_shift($parts);
            } else {
                break;
            }
        }
        //If we still have some combination of letters and digits, which were in the middle:
        //Those (from the end) which start with a digit, we add to houseNo
        while ($parts) {
            $last = end($parts);
            //If the last element consists only of numeric and punctuation, we consider it part of House Number
            if (preg_match('%^[0-9]%', $last)) {
                $houseNo = array_pop($parts).$houseNo;
            } else {
                break;
            }
        }
        //and the rest (if any) to street
        if ($parts) {
            $street .= ' '.implode(' ', $parts);
        }
        return [$street, $houseNo];
    }
}
