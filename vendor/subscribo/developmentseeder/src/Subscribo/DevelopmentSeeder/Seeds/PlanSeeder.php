<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\SubscriptionPlan;
use Subscribo\ModelCore\Models\DeliveryPlan;
use Subscribo\ModelCore\Models\BillingPlan;

/**
 * Class PlanSeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class PlanSeeder extends Seeder
{
    public function run()
    {
        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();

        $frontendWeeklyDeliveryPlan = new DeliveryPlan();
        $frontendWeeklyDeliveryPlan->deliveryPeriod = '1 week';
        $frontendWeeklyDeliveryPlan->deliveryAutoAddLimit = '2 months';
        $frontendWeeklyDeliveryPlan->deliveryAutoAvailableStart = 'today';
        $frontendWeeklyDeliveryPlan->deliveryAutoAvailableEnd = '1 month';
        $frontendWeeklyDeliveryPlan->seedStart = 'Tuesday';
        $frontendWeeklyDeliveryPlan->service()->associate($frontendService);
        $frontendWeeklyDeliveryPlan->save();
        $frontendWeeklyBillingPlan =  new BillingPlan();
        $frontendWeeklyBillingPlan->service()->associate($frontendService);
        $frontendWeeklyBillingPlan->save();

        $frontendWeeklyPlan = SubscriptionPlan::firstOrNew([
            'identifier' => 'WEEKLY',
            'service_id' => $frontendService->id
        ]);
        $frontendWeeklyPlan->deliveryPlan()->associate($frontendWeeklyDeliveryPlan);
        $frontendWeeklyPlan->billingPlan()->associate($frontendWeeklyBillingPlan);
        $frontendWeeklyPlan->save();

        $mainService = Service::query()->where(['identifier' => 'MAIN'])->first();
        $mainWeeklyDeliveryPlan = new DeliveryPlan();
        $mainWeeklyDeliveryPlan->deliveryAutoAddLimit = '2 months';
        $mainWeeklyDeliveryPlan->deliveryAutoAvailableEnd = '1 month';
        $mainWeeklyDeliveryPlan->seedStart = 'Monday';
        $mainWeeklyDeliveryPlan->service()->associate($mainService);
        $mainWeeklyDeliveryPlan->save();
        $mainWeeklyBillingPlan =  new BillingPlan();
        $mainWeeklyBillingPlan->service()->associate($mainService);
        $mainWeeklyBillingPlan->save();

        $mainWeeklyPlan = SubscriptionPlan::firstOrNew([
            'identifier' => 'WEEKLY',
            'service_id' => $mainService->id
        ]);
        $mainWeeklyPlan->deliveryPlan()->associate($mainWeeklyDeliveryPlan);
        $mainWeeklyPlan->billingPlan()->associate($mainWeeklyBillingPlan);
        $mainWeeklyPlan->save();

        $test3Service = Service::query()->where(['identifier' => 'TEST3'])->first();
        $test3MonthlyDeliveryPlan = new DeliveryPlan();
        $test3MonthlyDeliveryPlan->deliveryPeriod = '1 month';
        $test3MonthlyDeliveryPlan->deliveryAutoAddLimit = '5 months';
        $test3MonthlyDeliveryPlan->deliveryAutoAvailableStart = '2 weeks';
        $test3MonthlyDeliveryPlan->deliveryAutoAvailableEnd = '3 month';
        $test3MonthlyDeliveryPlan->service()->associate($test3Service);
        $test3MonthlyDeliveryPlan->save();
        $test3MonthlyBillingPlan =  new BillingPlan();
        $test3MonthlyBillingPlan->service()->associate($test3Service);
        $test3MonthlyBillingPlan->save();


        $test3MonthlyPlan = SubscriptionPlan::firstOrNew([
            'identifier' => 'MONTHLY',
            'service_id' => $test3Service->id
        ]);
        $test3MonthlyPlan->deliveryPlan()->associate($test3MonthlyDeliveryPlan);
        $test3MonthlyPlan->billingPlan()->associate($test3MonthlyBillingPlan);
        $test3MonthlyPlan->save();
    }
}
