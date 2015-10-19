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
        $frontendWeeklyBillingPlan->translateOrNew('en')->periodDenotation = 'weekly';
        $frontendWeeklyBillingPlan->translateOrNew('de')->periodDenotation = 'wochentlich';
        $frontendWeeklyBillingPlan->translateOrNew('sk')->periodDenotation = 'týždenne';
        $frontendWeeklyBillingPlan->translateOrNew('cs')->periodDenotation = 'týdenně';
        $frontendWeeklyBillingPlan->save();

        $frontendWeeklyPlan = SubscriptionPlan::firstOrNew([
            'identifier' => 'WEEKLY',
            'service_id' => $frontendService->id
        ]);
        $frontendWeeklyPlan->deliveryPlan()->associate($frontendWeeklyDeliveryPlan);
        $frontendWeeklyPlan->billingPlan()->associate($frontendWeeklyBillingPlan);
        $frontendWeeklyPlan->translateOrNew('en')->name = 'Weekly billing plan';
        $frontendWeeklyPlan->translateOrNew('en')->description = 'To be billed weekly and to get delivery weekly';
        $frontendWeeklyPlan->translateOrNew('sk')->name = 'Týždenný fakturačný plán';
        $frontendWeeklyPlan->translateOrNew('sk')->description = 'Ak chcete platiť týždenne a dostávať tovar týždenne';
        $frontendWeeklyPlan->translateOrNew('cs')->name = 'Týdenní fakturační plán';
        $frontendWeeklyPlan->translateOrNew('cs')->description = 'Pokud chcete platit týdenně a dostávať tovar týdenně';
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
        $mainWeeklyBillingPlan->translateOrNew('en')->periodDenotation = 'weekly';
        $mainWeeklyBillingPlan->translateOrNew('de')->periodDenotation = 'wöchentlich';
        $mainWeeklyBillingPlan->translateOrNew('sk')->periodDenotation = 'týždenne';
        $mainWeeklyBillingPlan->translateOrNew('cs')->periodDenotation = 'týdenně';
        $mainWeeklyBillingPlan->save();

        $mainWeeklyPlan = SubscriptionPlan::firstOrNew([
            'identifier' => 'WEEKLY',
            'service_id' => $mainService->id
        ]);
        $mainWeeklyPlan->deliveryPlan()->associate($mainWeeklyDeliveryPlan);
        $mainWeeklyPlan->billingPlan()->associate($mainWeeklyBillingPlan);
        $mainWeeklyPlan->translateOrNew('en')->name = 'Weekly billing plan';
        $mainWeeklyPlan->translateOrNew('en')->description = 'To be billed weekly and to get delivery weekly';
        $mainWeeklyPlan->translateOrNew('sk')->name = 'Týždenný fakturačný plán';
        $mainWeeklyPlan->translateOrNew('sk')->description = 'Ak chcete platiť týždenne a dostávať tovar týždenne';
        $mainWeeklyPlan->translateOrNew('cs')->name = 'Týdenní fakturační plán';
        $mainWeeklyPlan->translateOrNew('cs')->description = 'Pokud chcete platit týdenně a dostávať tovar týdenně';
        $mainWeeklyPlan->save();

        $mainMonthlyBillingPlan =  new BillingPlan();
        $mainMonthlyBillingPlan->service()->associate($mainService);
        $mainMonthlyBillingPlan->translateOrNew('en')->periodDenotation = 'monthly';
        $mainMonthlyBillingPlan->translateOrNew('de')->periodDenotation = 'monatlich';
        $mainMonthlyBillingPlan->translateOrNew('sk')->periodDenotation = 'mesačne';
        $mainMonthlyBillingPlan->translateOrNew('cs')->periodDenotation = 'měsíčně';
        $mainMonthlyBillingPlan->save();

        $mainMonthlyPlan = SubscriptionPlan::firstOrNew([
            'identifier' => 'MONTHLY',
            'service_id' => $mainService->id
        ]);
        $mainMonthlyPlan->deliveryPlan()->associate($mainWeeklyDeliveryPlan);
        $mainMonthlyPlan->billingPlan()->associate($mainMonthlyBillingPlan);
        $mainMonthlyPlan->translateOrNew('en')->name = 'Monthly billing plan';
        $mainMonthlyPlan->translateOrNew('en')->description = 'To be billed monthly and to get delivery weekly';
        $mainMonthlyPlan->translateOrNew('sk')->name = 'Mesačný fakturačný plán';
        $mainMonthlyPlan->translateOrNew('sk')->description = 'Ak chcete platiť mesačne a dostávať tovar týždenne';
        $mainMonthlyPlan->translateOrNew('cs')->name = 'Měsíční fakturační plán';
        $mainMonthlyPlan->translateOrNew('cs')->description = 'Pokud chcete platit měsíčně a dostávať tovar týdenně';
        $mainMonthlyPlan->save();

        $mainYearlyBillingPlan =  new BillingPlan();
        $mainYearlyBillingPlan->service()->associate($mainService);
        $mainYearlyBillingPlan->translateOrNew('en')->periodDenotation = 'yearly';
        $mainYearlyBillingPlan->translateOrNew('de')->periodDenotation = 'jährlich';
        $mainYearlyBillingPlan->translateOrNew('sk')->periodDenotation = 'ročne';
        $mainYearlyBillingPlan->translateOrNew('cs')->periodDenotation = 'ročně';
        $mainYearlyBillingPlan->save();

        $mainYearlyPlan = SubscriptionPlan::firstOrNew([
            'identifier' => 'YEARLY',
            'service_id' => $mainService->id
        ]);
        $mainYearlyPlan->deliveryPlan()->associate($mainWeeklyDeliveryPlan);
        $mainYearlyPlan->billingPlan()->associate($mainYearlyBillingPlan);
        $mainYearlyPlan->translateOrNew('en')->name = 'Yearly billing plan';
        $mainYearlyPlan->translateOrNew('en')->description = 'To be billed yearly and to get delivery weekly';
        $mainYearlyPlan->translateOrNew('sk')->name = 'Ročný fakturačný plán';
        $mainYearlyPlan->translateOrNew('sk')->description = 'Ak chcete platiť ročne a dostávať tovar týždenne';
        $mainYearlyPlan->translateOrNew('cs')->name = 'Roční fakturační plán';
        $mainYearlyPlan->translateOrNew('cs')->description = 'Pokud chcete platit ročně a dostávať tovar týdenně';
        $mainYearlyPlan->save();

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
