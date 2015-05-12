<!DOCTYPE html>
<html>
    <head>
        <title>Omnipay Klarna Invoice Driver Check Order Status Example page</title>
    </head>
<body>
    <h1>Omnipay Klarna Invoice Driver Check Order Status Example page</h1>
<?php

use Omnipay\Omnipay;

$merchantId = getenv('KLARNA_MERCHANT_ID');
$sharedSecret = getenv('KLARNA_SHARED_SECRET');

$capturePageUrlStub = 'https://your.web.site.example/path/to/capture?reservation_number=';
$checkPageUrlStub = 'https://your.web.site.example/path/to/check';

/** @var \Omnipay\Klarna\InvoiceGateway $gateway */
$gateway = Omnipay::create('Klarna\\Invoice');
$gateway->setMerchantId($merchantId)
    ->setSharedSecret($sharedSecret)
    ->setLocale('de_at')
    ->setTestMode(true);

$reservationNumber = isset($_GET['reservation_number']) ? $_GET['reservation_number'] : null;
$invoiceNumber =  isset($_GET['invoice_number']) ? $_GET['invoice_number'] : null;

echo '<h2>Gateway Name: '.$gateway->getName()."</h2>\n";
echo '<h3>Reservation Number: '.$reservationNumber."</h>\n";
echo '<h3>Invoice Number: '.$invoiceNumber."</h>\n";


$data = [
    'reservationNumber' => $reservationNumber,
    'invoiceNumber' => $invoiceNumber,
];



try {
    $request = $gateway->checkOrderStatus($data);

    $response = $request->send();

    echo '<p>Order status:'.$response->getOrderStatus().'</p>';

    if ($response->isSuccessful()) {
        echo '<h3>Order has been approved</h3>';
        if ($reservationNumber) {
            echo "<ul>\n";
            echo '<li><a href="'.$capturePageUrlStub.$reservationNumber.'">Capture whole amount</a></li>';
            echo '<li><a href="'.$capturePageUrlStub.$reservationNumber.'&part=1">Capture part of amount</a></li>';
            echo "</ul>\n";
        }
    } elseif ($response->isPending()) {
        echo '<h3>Order is still pending</h3>';
        if ($reservationNumber) {
            echo '<a href="'.$checkPageUrlStub.'?reservation_number='.$reservationNumber.'">Check again (with reservation number)</a>';
        }
        if ($invoiceNumber) {
            echo '<a href="'.$checkPageUrlStub.'?invoice_number='.$invoiceNumber.'">Check again (with invoice number)</a>';
        }
    } elseif ($response->isDenied()) {
        echo '<h3>Order has been denied</h3>';
    }
} catch (KlarnaException $e) {
    echo '<p>KlarnaException occurred: '.$e->getMessage().' (Code: '.$e->getCode().')</p>';
} catch (\Exception $e) {
    echo '<p>Some error occurred: '.$e->getMessage().' (Code: '.$e->getCode().')</p>';
}
?>
    </body>
</html>
