<html>
    <head>
        <title>Omnipay Klarna Driver Authorize Invoice Example page</title>
    </head>
    <body>
        <h1>Omnipay Klarna Driver Authorize Invoice Example page</h1>
<?php

use Omnipay\Omnipay;

$merchantId = getenv('KLARNA_MERCHANT_ID');
$sharedSecret = getenv('KLARNA_SHARED_SECRET');

$amount = '1.19';

/** @var \Omnipay\Klarna\InvoiceGateway $gateway */
$gateway = Omnipay::create('Klarna\\Invoice');
$gateway->setMerchantId($merchantId)
    ->setSharedSecret($sharedSecret)
    ->setLocale('de_at');

echo '<h2> Gateway Name: '.$gateway->getName()."<h2>\n";
$card = [

];
$data = [
    'amount' => $amount,
    'card' => $card,
];
$shoppingCart = [

];
$request = $gateway->authorize($data);
$request->setItems($shoppingCart);
$response = $request->send();


?>
    </body>
</html>
