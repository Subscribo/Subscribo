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
                ],
            ];
        } else {
            $payUnityConfigData = [];
        }

        $payUnityConfigDataCard = $payUnityConfigData;
        $payUnityConfigDataSofort = $payUnityConfigData;
        if ($payUnityConfigDataCard) {
            $payUnityConfigDataCard['purchase'] = [
                'registrationMode' => true,
                'brands' => 'VISA MASTER MAESTRO',
            ];
        }
        if ($payUnityConfigDataSofort) {
            $payUnityConfigDataSofort['purchase'] = [
                'registrationMode' => false,
                'brands' => 'SOFORTUEBERWEISUNG',
            ];
        }

        $payUnityCopyAndPay = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY']);
        $payUnityCopyAndPaySofort = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY-SOFORT']);
        $payUnityPost = TransactionGateway::firstOrCreate(['identifier' => 'PAY_UNITY-POST']);
        $klarnaInvoice = TransactionGateway::firstOrCreate(['identifier' => 'KLARNA-INVOICE']);

        $austria = Country::firstOrCreate(['identifier' => 'AT']);
        $germany = Country::firstOrCreate(['identifier' => 'DE']);
        $slovakia = Country::firstOrCreate(['identifier' => 'SK']);

        $euro = Currency::firstOrCreate(['identifier' => 'EUR']);


        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();
        $mainService = Service::query()->where(['identifier' => 'MAIN'])->first();

        /* Frontend Service */

        $klarnaConfigForFrontend = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'transaction_gateway_id' => $klarnaInvoice->id,
            'country_id' => $austria->id,

        ]);
        $klarnaConfigForFrontend->configuration = $klarnaConfigData;
        $klarnaConfigForFrontend->isDefault = true;
        $klarnaConfigForFrontend->ordering = 1;
        $klarnaConfigForFrontend->save();

        $payUnityConfigForFrontendPost = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'transaction_gateway_id' => $payUnityPost->id,
        ]);
        $payUnityConfigForFrontendPost->configuration = $payUnityConfigData;
        $payUnityConfigForFrontendPost->ordering = 4;
        $payUnityConfigForFrontendPost->save();

        $payUnityConfigForFrontendCard = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'transaction_gateway_id' => $payUnityCopyAndPay->id,
        ]);
        $payUnityConfigForFrontendCard->configuration = $payUnityConfigDataCard;
        $payUnityConfigForFrontendCard->ordering = 2;
        $payUnityConfigForFrontendCard->parent()->associate($payUnityConfigForFrontendPost);
        $payUnityConfigForFrontendCard->save();

        $payUnityConfigForFrontendSofort = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'transaction_gateway_id' => $payUnityCopyAndPaySofort->id,
        ]);
        $payUnityConfigForFrontendSofort->configuration = $payUnityConfigDataSofort;
        $payUnityConfigForFrontendSofort->ordering = 3;
        $payUnityConfigForFrontendSofort->parent()->associate($payUnityConfigForFrontendPost);
        $payUnityConfigForFrontendSofort->save();

        /* Main Service */

        $payUnityConfigForMainPost = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $mainService->id,
            'transaction_gateway_id' => $payUnityPost->id,
        ]);
        $payUnityConfigForMainPost->configuration = $payUnityConfigData;
        $payUnityConfigForMainPost->ordering = 4;
        $payUnityConfigForMainPost->save();

        $payUnityConfigForMainCard = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $mainService->id,
            'transaction_gateway_id' => $payUnityCopyAndPay->id,
        ]);
        $payUnityConfigForMainCard->configuration = $payUnityConfigDataCard;
        $payUnityConfigForMainCard->ordering = 2;
        $payUnityConfigForMainCard->parent()->associate($payUnityConfigForMainPost);
        $payUnityConfigForMainCard->save();

        $payUnityConfigForMainSofort = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $mainService->id,
            'transaction_gateway_id' => $payUnityCopyAndPaySofort->id,
        ]);
        $payUnityConfigForMainSofort->configuration = $payUnityConfigDataSofort;
        $payUnityConfigForMainSofort->ordering = 3;
        $payUnityConfigForMainSofort->parent()->associate($payUnityConfigForMainPost);
        $payUnityConfigForMainSofort->save();

        $klarnaConfigForMain = TransactionGatewayConfiguration::firstOrCreate([
            'service_id' => $mainService->id,
            'transaction_gateway_id' => $klarnaInvoice->id,
            'country_id' => $austria->id,

        ]);
        $klarnaConfigForMain->configuration = $klarnaConfigData;
        $klarnaConfigForMain->ordering = 1;
        $klarnaConfigForMain->save();
    }
}
