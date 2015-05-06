<?php

namespace Omnipay\Klarna\Message;

use Klarna;
use KlarnaCountry;
use KlarnaLanguage;
use KlarnaCurrency;
use Omnipay\Klarna\Message\AbstractInvoiceRequest;
use Omnipay\Klarna\Message\InvoiceAuthorizeResponse;
use Omnipay\Klarna\Traits\InvoiceGatewayDefaultParametersGettersAndSettersTrait;
use Omnipay\Common\Exception\InvalidRequestException;


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
        $items = $this->getItems();
        $data['articles'] = $items ? $items->all() : [];
        return $data;
    }

    public function sendData($data)
    {
        if (( ! is_array($data))) {
            throw new \InvalidArgumentException('Data parameter should be an array');
        }
        $k = new Klarna();
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
        /** @var \Subscribo\Omnipay\Shared\Item $article */
        foreach ($data['articles'] as $article) {
            $k->addArticle(
                $article->getQuantity(),
                $article->getIdentifier(),
                $article->getName(),
                $article->getPrice(),
                $article->getTaxPercent(),
                $article->getDiscountPercent(),
                $article->getFlags()
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

}
