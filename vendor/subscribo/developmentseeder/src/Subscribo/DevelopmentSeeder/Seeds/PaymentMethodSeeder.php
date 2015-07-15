<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $payUnityCopyAndPay = PaymentMethod::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY']);
        $klarnaInvoice = PaymentMethod::firstOrCreate(['identifier' => 'KLARNA-INVOICE']);

        $payUnityCopyAndPay->name = 'PayUnity';
        $payUnityCopyAndPay->description = 'For Credit / Debit card payments, SOFORT UEBERWEISUNG...';
        $payUnityCopyAndPay->save();

        $klarnaInvoice->name = 'Klarna Invoice';
        $klarnaInvoice->description = 'To be invoiced by Klarna';
        $klarnaInvoice->save();
    }
}
