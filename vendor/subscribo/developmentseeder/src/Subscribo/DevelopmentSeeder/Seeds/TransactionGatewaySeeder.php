<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\TransactionGateway;
use Subscribo\TransactionPluginKlarna\Drivers\InvoiceDriver;
use Subscribo\TransactionPluginPayUnity\Drivers\CopyAndPayDriver;
use Subscribo\TransactionPluginPayUnity\Drivers\PostDriver;


class TransactionGatewaySeeder extends Seeder
{
    public function run()
    {
        $payUnityCopyAndPay = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY']);
        $payUnityCopyAndPaySofort = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY-SOFORT']);
        $payUnityPost = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-POST']);
        $klarnaInvoice = TransactionGateway::firstOrCreate(['identifier' => 'KLARNA-INVOICE']);

        $payUnityCopyAndPay->name = 'PayUnity Card';
        $payUnityCopyAndPay->driver = CopyAndPayDriver::getDriverIdentifier();
        $payUnityCopyAndPay->description = 'For Credit / Debit card payments...';
        $payUnityCopyAndPay->save();

        $payUnityCopyAndPaySofort->name = 'PayUnity Sofort';
        $payUnityCopyAndPaySofort->driver = CopyAndPayDriver::getDriverIdentifier();
        $payUnityCopyAndPaySofort->description = 'For SOFORT UEBERWEISUNG...';
        $payUnityCopyAndPaySofort->save();

        $payUnityPost->name = 'PayUnity Offline';
        $payUnityPost->driver = PostDriver::getDriverIdentifier();
        $payUnityPost->description = 'For registered customers, who have already paid via Card';
        $payUnityPost->save();

        $klarnaInvoice->name = 'Klarna Invoice';
        $klarnaInvoice->driver = InvoiceDriver::getDriverIdentifier();
        $klarnaInvoice->description = 'To be invoiced by Klarna';
        $klarnaInvoice->save();
    }
}
