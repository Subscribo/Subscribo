<!DOCTYPE html>
<html>
    <head>
        <title>Omnipay Klarna Driver Prepare Invoice Example page</title>
    </head>
    <body>
        <h1>Omnipay Klarna Driver Prepare Invoice Example page</h1>
<?php

use Omnipay\Omnipay;

$merchantId = env('KLARNA_MERCHANT_ID');
$sharedSecret = env('KLARNA_SHARED_SECRET');

$authorizePageUrl = 'https://your.web.site.example/path/to/authorize';

/** @var \Omnipay\Klarna\InvoiceGateway $gateway */
$gateway = Omnipay::create('Klarna\\Invoice');
$gateway->setMerchantId($merchantId)
    ->setLanguage('de')
    ->setCountry('at')
    ->setCurrency('eur');

?>
        <h2> Gateway Name: <?php echo $gateway->getName(); ?></h2>
        <h3> You can try to be invoiced on our behalf. Bellow you can select preferred workflow.</h3>

        <form action="<?php echo $authorizePageUrl ?>" method="POST">
            <button type="submit" name="workflow" value="approved">Approved</button>
            <button type="submit" name="workflow" value="pending-approved">Pending -> Approved</button>
            <button type="submit" name="workflow" value="pending-denied">Pending -> Denied</button>
            <button type="submit" name="workflow" value="denied">Denied</button>
        </form>
    </body>
</html>
