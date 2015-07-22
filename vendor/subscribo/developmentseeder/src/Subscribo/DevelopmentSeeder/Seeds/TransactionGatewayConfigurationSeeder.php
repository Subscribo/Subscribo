<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\TransactionGatewayConfiguration;
use Subscribo\ModelCore\Models\TransactionGateway;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\Country;
use Subscribo\ModelCore\Models\Currency;


class TransactionGatewayConfigurationSeeder extends Seeder
{
    public function run()
    {
        if (env('KLARNA_SHARED_SECRET')) {
            $klarnaConfigData = [
                'merchantId' => env('KLARNA_MERCHANT_ID'),
                'sharedSecret' => env('KLARNA_SHARED_SECRET'),
                'locale' => 'de_at',
                'testMode' => true,
            ];
        } else {
            $klarnaConfigData = [];
        }

        if (false) {
            $payUnityConfigData = [
                'testMode' => true,
            ];
        } else {
            $payUnityConfigData = [];
        }

        $payUnityCopyAndPay = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY']);
        $klarnaInvoice = TransactionGateway::firstOrCreate(['identifier' => 'KLARNA-INVOICE']);

        $austria = Country::firstOrCreate(['identifier' => 'AT']);
        $germany = Country::firstOrCreate(['identifier' => 'DE']);
        $slovakia = Country::firstOrCreate(['identifier' => 'SK']);

        $euro = Currency::firstOrCreate(['identifier' => 'EUR']);


        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();

        $payUnityConfigForFrontend = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'transaction_gateway_id' => $payUnityCopyAndPay->id,
        ]);

        $klarnaConfigForFrontend = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'transaction_gateway_id' => $klarnaInvoice->id,
            'country_id' => $austria->id,

        ]);

        $klarnaConfigForFrontend->configuration = json_encode($klarnaConfigData);
        $klarnaConfigForFrontend->isDefault = true;
        $klarnaConfigForFrontend->ordering = 1;
        $klarnaConfigForFrontend->save();

        $payUnityConfigForFrontend->configuration = json_encode($payUnityConfigData);
        $payUnityConfigForFrontend->ordering = 2;
        $payUnityConfigForFrontend->save();
    }
}
