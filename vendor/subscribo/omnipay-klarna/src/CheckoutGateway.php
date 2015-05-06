<?php

namespace Omnipay\Klarna;

use Omnipay\Klarna\AbstractGateway;

class CheckoutGateway extends AbstractGateway
{

    public function getName()
    {
        return 'Klarna Checkout';
    }

}
