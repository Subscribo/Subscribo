<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\TransactionGateway;
use Subscribo\TransactionPluginKlarna\Drivers\InvoiceDriver;
use Subscribo\TransactionPluginPayUnity\Drivers\CopyAndPayDriver;

class TransactionGatewaySeeder extends Seeder
{
    public function run()
    {
        $payUnityCopyAndPay = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY']);
        $klarnaInvoice = TransactionGateway::firstOrCreate(['identifier' => 'KLARNA-INVOICE']);

        $payUnityCopyAndPay->name = 'PayUnity';
        $payUnityCopyAndPay->driver = CopyAndPayDriver::getDriverIdentifier();
        $payUnityCopyAndPay->description = 'For Credit / Debit card payments, SOFORT UEBERWEISUNG...';
        $payUnityCopyAndPay->save();

        $klarnaInvoice->name = 'Klarna Invoice';
        $klarnaInvoice->driver = InvoiceDriver::getDriverIdentifier();
        $klarnaInvoice->description = 'To be invoiced by Klarna';
        $klarnaInvoice->save();
    }
}
