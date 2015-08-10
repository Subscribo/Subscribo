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
                'initialize' => [
                    'merchantId' => env('KLARNA_MERCHANT_ID'),
                    'sharedSecret' => env('KLARNA_SHARED_SECRET'),
                    'locale' => 'de_at',
                    'testMode' => true,
                ],
            ];
        } else {
            $klarnaConfigData = [];
        }

        if (env('PAYUNITY_TRANSACTION_CHANNEL')) {
            $payUnityConfigData = [
                'initialize' => [
                    'securitySender' => env('PAYUNITY_SECURITY_SENDER'),
                    'transactionChannel' => env('PAYUNITY_TRANSACTION_CHANNEL'),
                    'userLogin' => env('PAYUNITY_USER_LOGIN'),
                    'userPwd' => env('PAYUNITY_USER_PWD'),
                    'testMode' => true,
                    'registrationMode' => true,
                ],
                'purchase' => [
                    'brands' => 'VISA MASTER MAESTRO SOFORTUEBERWEISUNG',
                ],
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
        $mainService = Service::query()->where(['identifier' => 'MAIN'])->first();


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

        $payUnityConfigForMain = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $mainService->id,
            'transaction_gateway_id' => $payUnityCopyAndPay->id,
        ]);

        $klarnaConfigForMain = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $mainService->id,
            'transaction_gateway_id' => $klarnaInvoice->id,
            'country_id' => $austria->id,

        ]);

        $payUnityConfigForMain->configuration = json_encode($payUnityConfigData);
        $payUnityConfigForMain->ordering = 2;
        $payUnityConfigForMain->save();

        $klarnaConfigForMain->configuration = json_encode($klarnaConfigData);
        $payUnityConfigForMain->isDefault = true;
        $klarnaConfigForMain->ordering = 1;
        $klarnaConfigForMain->save();
    }
}
