<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\PaymentConfiguration;
use Subscribo\ModelCore\Models\PaymentMethod;
use Subscribo\ModelCore\Models\Service;


class PaymentConfigurationSeeder extends Seeder
{
    public function run()
    {
        $payUnityCopyAndPay = PaymentMethod::firstOrCreate(['identifier' => 'PAY_UNITY-COPY_AND_PAY']);
        $klarnaInvoice = PaymentMethod::firstOrCreate(['identifier' => 'KLARNA-INVOICE']);

        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();

        $payUnityConfigForFrontend = PaymentConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'payment_method_id' => $payUnityCopyAndPay->id,
        ]);

        $klarnaConfigForFrontend = PaymentConfiguration::firstOrCreate([
            'service_id' => $frontendService->id,
            'payment_method_id' => $klarnaInvoice->id,
        ]);

    }
}
