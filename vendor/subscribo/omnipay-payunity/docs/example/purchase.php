<html>
    <head>
        <title>Omnipay PayUnity Driver Purchase Example page</title>
    </head>
    <body>
        <h1>Omnipay PayUnity Driver Purchase Example page</h1>

<?php
/**
 * Widget generation example
 */

$amount = '3.45';


/** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
$gateway = \Omnipay\Omnipay::create('PayUnity\\COPYandPAY');

$gateway->initialize([
    "securitySender" => "696a8f0fabffea91517d0eb0a0bf9c33",
    "transactionChannel" => "52275ebaf361f20a76b038ba4c806991",
    "userLogin" => "1143238d620a572a726fe92eede0d1ab",
    "transactionMode" => "CONNECTOR_TEST",
    "userPwd" => "demo",
    'testMode' => true,
]);


$response = $gateway->purchase([
    'amount' => $amount,
    'currency' => 'EUR',
    'brands' => 'VISA MASTER MAESTRO SOFORTUEBERWEISUNG',
    'returnUrl' => 'https://localhost/example/complete/purchase',
])->send();

if ( ! $response->haveWidget()) {
    echo '<div>Some error have occurred.</div>';
} else {
    $widget = $response->getWidget();
    echo '<div>You can try to pay us '.$amount.' Euro using the form bellow.</div>';
    echo $widget;
}
?>

    </body>
</html>
