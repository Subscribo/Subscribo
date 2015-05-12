<!DOCTYPE html>
<html>
    <head>
        <title>Omnipay Klarna Driver Capture Invoice Example page</title>
    </head>
<body>
    <h1>Omnipay Klarna Driver Capture Invoice Example page</h1>
<?php

use Omnipay\Omnipay;
use KlarnaFlags;

$merchantId = getenv('KLARNA_MERCHANT_ID');
$sharedSecret = getenv('KLARNA_SHARED_SECRET');

$capturePageUrlStub = 'https://your.web.site.example/path/to/capture?reservation_number=';
$checkPageUrlStubReservation = 'https://your.web.site.example/path/to/check?reservation_number=';
$checkPageUrlStubInvoice = 'https://your.web.site.example/path/to/check?invoice_number=';

/** @var \Omnipay\Klarna\InvoiceGateway $gateway */
$gateway = Omnipay::create('Klarna\\Invoice');
$gateway->setMerchantId($merchantId)
    ->setSharedSecret($sharedSecret)
    ->setLocale('de_at')
    ->setTestMode(true);

$reservationNumber = isset($_GET['reservation_number']) ? $_GET['reservation_number'] : null;
$partial = isset($_GET['part']) ? $_GET['part'] : null;

echo '<h2>Gateway Name: '.$gateway->getName()."</h2>\n";
echo '<h3>Reservation Number: '.$reservationNumber."</h>\n";


$data = [
    'reservationNumber' => $reservationNumber,
];

try {
    $request = $gateway->capture($data);

    if ($partial) {
        echo "<p>Partial activation</p>\n";
        $selectedItems = [
            [
                'identifier' => 'E01',
                'quantity' => 2,
            ],
            [
                'identifier' => 'HANDLING',
                'quantity' => 1,
            ],
        ];
        $request->setItems($selectedItems);
    }

    $response = $request->send();

    $transactionReference = $response->getTransactionReference();
    $invoiceNumber = $response->getInvoiceNumber();
    echo '<p>Invoice number:'.$invoiceNumber.'</p>';
    echo '<p>Transaction Reference:'.$transactionReference.'</p>';

    echo '<p>Risk status:'.$response->getRiskStatus().'</p>';

    if ($response->isSuccessful()) {
        echo '<h3>Capture has been approved</h3>';
        echo "<ul>\n";
        echo '<li><a href="'.$checkPageUrlStubReservation.$reservationNumber.'">Check by reservation number</a></li>';
        echo '<li><a href="'.$checkPageUrlStubInvoice.$invoiceNumber.'">Check by invoice number</a></li>';
        if ($partial) {
            echo '<li><a href="'.$capturePageUrlStub.$reservationNumber.'">Capture whole amount</a></li>';
            echo '<li><a href="'.$capturePageUrlStub.$reservationNumber.'&part=1">Capture part of amount</a></li>';
        }
        echo "</ul>\n";

    } else {
        echo '<h3>Capture has not been approved</h3>';
        echo "<ul>\n";
        echo '<li><a href="'.$checkPageUrlStubReservation.$reservationNumber.'">Check by reservation number</a></li>';
        echo '<li><a href="'.$checkPageUrlStubInvoice.$invoiceNumber.'">Check by invoice number</a></li>';
        echo "</ul>\n";
    }
} catch (KlarnaException $e) {
    echo '<p>KlarnaException occurred: '.$e->getMessage().' (Code: '.$e->getCode().')</p>';
} catch (\Exception $e) {
    echo '<p>Some error occurred: '.$e->getMessage().' (Code: '.$e->getCode().')</p>';
}
?>
    </body>
</html>
