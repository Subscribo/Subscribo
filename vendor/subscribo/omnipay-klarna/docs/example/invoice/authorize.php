<!DOCTYPE html>
<html>
    <head>
        <title>Omnipay Klarna Driver Authorize Invoice Example page</title>
    </head>
    <body>
        <h1>Omnipay Klarna Driver Authorize Invoice Example page</h1>
<?php

use Omnipay\Omnipay;
use KlarnaFlags;
use KlarnaException;

$merchantId = getenv('KLARNA_MERCHANT_ID');
$sharedSecret = getenv('KLARNA_SHARED_SECRET');

$capturePageUrlStub = 'https://your.web.site.example/path/to/capture?reservation_number=';
$checkPageUrlStub = 'https://your.web.site.example/path/to/check?reservation_number=';

/** @var \Omnipay\Klarna\InvoiceGateway $gateway */
$gateway = Omnipay::create('Klarna\\Invoice');
$gateway->setMerchantId($merchantId)
    ->setSharedSecret($sharedSecret)
    ->setLocale('de_at')
    ->setTestMode(true);

$workflow = isset($_POST['workflow']) ? $_POST['workflow'] : null;

echo '<h2>Gateway Name: '.$gateway->getName()."</h2>\n";
echo '<h3>Selected workflow: '.$workflow."</h>\n";


switch ($workflow) {
    case 'approved':
        $email = 'youremail@email.com';
        $denied = false;
    break;
    case 'pending-approved':
        $email = 'pending_accepted@klarna.com';
        $denied = false;
    break;
    case 'pending-denied':
        $email = 'pending_denied@klarna.com';
        $denied = false;
    break;
    case 'denied':
    default:
        $email = 'youremail@email.com';
        $denied = true;
};

if ($denied) {
    $card = [
        'gender' => 'Female',
        'birthday' => '1980-04-14',
        'firstName' => 'Testperson-at',
        'lastName' => 'Denied',
        'address1' => 'Klarna-Straße 1/2/3',
        'address2' => null,
        'postCode' => '8070',
        'city'     => 'Hausmannstätten',
        'country'  => 'at',
        'phone'    => '0676 2800000',
        'email'    => $email,
    ];
} else {
    $card = [
        'gender' => 'Male',
        'birthday' => '1960-04-14',
        'firstName' => 'Testperson-at',
        'lastName' => 'Approved',
        'address1' => 'Klarna-Straße 1/2/3',
        'address2' => null,
        'postCode' => '8071',
        'city'     => 'Hausmannstätten',
        'country'  => 'at',
        'phone'    => '0676 2600000',
        'email'    => $email,
    ];
}

$data = [
    'card' => $card,
];
$shoppingCart = [
    [
        'name' => 'Example Article',
        'identifier' => 'E01',
        'price' => '4.00',
        'quantity' => 10,
        'taxPercent' => '20',
        'discountPercent' => '10',
    ],
    [
        'name' => 'Handling fee',
        'identifier' => 'HANDLING',
        'price' => '1.00',
        'quantity' => 5,
        'flags' => KlarnaFlags::IS_HANDLING,
    ],
];

try {
    $request = $gateway->authorize($data);
    $request->setItems($shoppingCart);
    $response = $request->send();
    $reservationNumber = $response->getReservationNumber();
    echo '<p>Reservation number:'.$reservationNumber.'</p>';

    echo '<p>Invoice status:'.$response->getInvoiceStatus().'</p>';

    if ($response->isSuccessful()) {
        echo '<h3>Authorization request was resolved</h3>';
        echo "<ul>\n";
        echo '<li><a href="'.$capturePageUrlStub.$reservationNumber.'">Capture whole amount</a></li>';
        echo '<li><a href="'.$capturePageUrlStub.$reservationNumber.'&part=1">Capture part of amount</a></li>';
        echo "</ul>\n";
    } else {
        echo '<h3>Authorization request is pending</h3>';
        echo '<a href="'.$checkPageUrlStub.$reservationNumber.'">Check again</a>';
    }
} catch (KlarnaException $e) {
    echo '<p>KlarnaException occurred: '.$e->getMessage().' (Code: '.$e->getCode().')</p>';
} catch (\Exception $e) {
    echo '<p>Some error occurred: '.$e->getMessage().' (Code: '.$e->getCode().')</p>';
}
?>
    </body>
</html>
