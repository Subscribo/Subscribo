<!DOCTYPE html>
<html>
    <head>
        <title>POST recurring purchase example page</title>
    </head>
    <body>
        <h1>POST recurring purchase example page</h1>
<?php

$urlBase = 'https://your.site.example/example/Post/';

$amount = '1.05';

try {
    $cardReference = isset($_POST['reference']) ? $_POST['reference'] : '';

    /** @var \Omnipay\PayUnity\PostGateway $gateway */
    $gateway = \Omnipay\Omnipay::create('PayUnity\\Post');
    $gateway->setTestMode(true);
    $gateway->setSecuritySender('696a8f0fabffea91517d0eb0a0bf9c33');
    $gateway->setTransactionChannel('52275ebaf361f20a76b038ba4c806991');
    $gateway->setUserLogin('1143238d620a572a726fe92eede0d1ab');
    $gateway->setUserPwd('demo');


    $request = $gateway->purchase();
    $request->setCardReference($cardReference);
    $request->setAmount($amount);
    $request->setCurrency('EUR');

    $response = $request->send();
    $transactionReference = $response->getTransactionReference();

    echo '<div>'.($response->isSuccessful() ? 'Success' : 'Failure').'</div>';
    echo '<div>Message: '.$response->getMessage().'</div>';
    echo '<div>Code: '.$response->getCode().'</div>';
    echo '<div>Transaction reference:'.$transactionReference.'</div>';
    echo '<h4>Data:</h4>';
    echo '<code>';
    var_dump($response->getData());
    echo '</code>';
?>
    <form action="<?php echo $urlBase; ?>purchase" method="post" target="_blank">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
        <button type="submit">Make recurring payment with amount 1.05 Euro</button>
    </form>
    <br>
    <form action="<?php echo $urlBase; ?>void" method="post" target="_blank">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
        <label for="reference">Transaction reference:</label>
        <input type="text" name="transaction" style="width:40em" value="<?php echo $transactionReference ?>">
        <button type="submit">Void transaction</button>
    </form>
    <br>
    <form action="<?php echo $urlBase; ?>refund" method="post" target="_blank">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
        <label for="reference">Transaction reference:</label>
        <input type="text" name="transaction" style="width:40em" value="<?php echo $transactionReference ?>">
        <button type="submit">Partial refund 0.50 Euro</button>
    </form>
<?php

} catch (Exception $e) {
    echo '<div>An error happened. Code: '.$e->getCode().' Message: '.$e->getMessage().'</div>';
}
?>
    </body>
</html>
