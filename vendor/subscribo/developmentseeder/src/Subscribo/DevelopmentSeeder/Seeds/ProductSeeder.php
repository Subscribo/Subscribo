<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Product;
use Subscribo\ModelCore\Models\Price;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\SubscriptionPlan;
use Subscribo\ModelCore\Models\TaxCategory;
use Subscribo\ModelCore\Models\Currency;
use Subscribo\ModelCore\Models\Country;


/**
 * Class ProductSeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class ProductSeeder extends Seeder
{
    public function run()
    {
        $euro = Currency::firstOrCreate(['identifier' => 'EUR']);

        $noTaxCategory = TaxCategory::firstOrCreate(['identifier' => 'FREE']);
        $standardTaxCategory = TaxCategory::firstOrCreate(['identifier' => 'STANDARD']);
        $foodCategory = TaxCategory::firstOrCreate(['identifier' => 'FOOD']);

        $austria = Country::firstOrCreate(['identifier' => 'AT']);
        $germany = Country::firstOrCreate(['identifier' => 'DE']);
        $slovakia = Country::firstOrCreate(['identifier' => 'SK']);


        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();

        $frontendWeeklyPlan = SubscriptionPlan::query()->where([
            'identifier' => 'WEEKLY',
            'service_id' => $frontendService->id
        ])->first();

        $product1 = Product::firstOrNew(['identifier' => 'Product1']);
        $product1->standalone = true;

        $product1->service()->associate($frontendService);
        $product1->taxCategory()->associate($standardTaxCategory);
        $product1->subscriptionPlan()->associate($frontendWeeklyPlan);
        $product1->save();
        $product1->translateOrNew('en')->name = 'Product 1';
        $product1->translateOrNew('de')->name = 'Produkt 1';
        $product1->save();
        $price1 = Price::firstOrNew(['product_id' => $product1->id]);
        $price1->service()->associate($frontendService);
        $price1->amount = '12.50';
        $price1->currency()->associate($euro);
        $price1->everywhere = true;
        $price1->save();

        $product2 = Product::firstOrNew(['identifier' => 'Product2']);
        $product2->standalone = true;

        $product2->service()->associate($frontendService);
        $product2->taxCategory()->associate($standardTaxCategory);
        $product2->subscriptionPlan()->associate($frontendWeeklyPlan);
        $product2->save();
        $product2->translateOrNew('en')->name = 'Product 2';
        $product2->translateOrNew('de')->name = 'Produkt 2';
        $product2->save();
        $price2GermanyAustria = Price::firstOrNew([
            'product_id' => $product2->id,
            'amount' => '20',
        ]);
        $price2GermanyAustria->service()->associate($frontendService);
        $price2GermanyAustria->currency()->associate($euro);
        $price2GermanyAustria->save();
        $price2GermanyAustria->countries()->attach($austria);
        $price2GermanyAustria->countries()->attach($germany);
        $price2GermanyAustria->save();

        $price2Slovakia = Price::firstOrNew([
            'product_id' => $product2->id,
            'amount' => '15.50',
        ]);
        $price2Slovakia->service()->associate($frontendService);
        $price2Slovakia->currency()->associate($euro);
        $price2Slovakia->save();
        $price2Slovakia->countries()->attach($slovakia);
        $price2Slovakia->save();

        $product3 = Product::firstOrNew(['identifier' => 'Product3']);
        $product3->standalone = true;

        $product3->service()->associate($frontendService);
        $product3->taxCategory()->associate($standardTaxCategory);
        $product3->subscriptionPlan()->associate($frontendWeeklyPlan);
        $product3->save();
        $product3->translateOrNew('en')->name = 'Product 3';
        $product3->translateOrNew('de')->name = 'Produkt 3';
        $product3->save();

        $price3 = Price::firstOrNew(['product_id' => $product3->id]);
        $price3->service()->associate($frontendService);
        $price3->amount = '10.20';
        $price3->currency()->associate($euro);
        $price3->priceType = 'gross';
        $price3->everywhere = true;
        $price3->save();


        $mainService = Service::query()->where(['identifier' => 'MAIN'])->first();

        $mainWeeklyPlan = SubscriptionPlan::query()->where([
            'identifier' => 'WEEKLY',
            'service_id' => $mainService->id
        ])->first();

        $mainMonthlyPlan = SubscriptionPlan::query()->where([
            'identifier' => 'MONTHLY',
            'service_id' => $mainService->id
        ])->first();

        $mainYearlyPlan = SubscriptionPlan::query()->where([
            'identifier' => 'YEARLY',
            'service_id' => $mainService->id
        ])->first();


        $bread1 = Product::firstOrNew(['identifier' => 'WEAKLY_BREAD_PAID_WEEKLY']);
        $bread1->standalone = true;

        $bread1->service()->associate($mainService);
        $bread1->taxCategory()->associate($foodCategory);
        $bread1->subscriptionPlan()->associate($mainWeeklyPlan);
        $bread1->save();
        $bread1->translateOrNew('en')->name = 'Bread';
        $bread1->translateOrNew('de')->name = 'Brot';
        $bread1->translateOrNew('sk')->name = 'Chlieb';
        $bread1->translateOrNew('cz')->name = 'Chléb';
        $bread1->save();
        $bread1Price = Price::firstOrNew(['product_id' => $bread1->id]);
        $bread1Price->service()->associate($mainService);
        $bread1Price->amount = '1.00';
        $bread1Price->currency()->associate($euro);
        $bread1Price->everywhere = true;
        $bread1Price->save();

        $bread2 = Product::firstOrNew(['identifier' => 'WEAKLY_BREAD_PAID_MONTHLY']);
        $bread2->standalone = true;

        $bread2->service()->associate($mainService);
        $bread2->taxCategory()->associate($foodCategory);
        $bread2->subscriptionPlan()->associate($mainMonthlyPlan);
        $bread2->save();
        $bread2->translateOrNew('en')->name = 'Bread';
        $bread2->translateOrNew('de')->name = 'Brot';
        $bread2->translateOrNew('sk')->name = 'Chlieb';
        $bread2->translateOrNew('cz')->name = 'Chléb';
        $bread2->save();
        $bread2Price = Price::firstOrNew(['product_id' => $bread2->id]);
        $bread2Price->service()->associate($mainService);
        $bread2Price->amount = '3.00';
        $bread2Price->currency()->associate($euro);
        $bread2Price->everywhere = true;
        $bread2Price->save();

        $bread3 = Product::firstOrNew(['identifier' => 'WEAKLY_BREAD_PAID_YEARLY']);
        $bread3->standalone = true;

        $bread3->service()->associate($mainService);
        $bread3->taxCategory()->associate($foodCategory);
        $bread3->subscriptionPlan()->associate($mainYearlyPlan);
        $bread3->save();
        $bread3->translateOrNew('en')->name = 'Bread';
        $bread3->translateOrNew('de')->name = 'Brot';
        $bread3->translateOrNew('sk')->name = 'Chlieb';
        $bread3->translateOrNew('cz')->name = 'Chléb';
        $bread3->save();
        $bread3Price = Price::firstOrNew(['product_id' => $bread3->id]);
        $bread3Price->service()->associate($mainService);
        $bread3Price->amount = '30.00';
        $bread3Price->currency()->associate($euro);
        $bread3Price->everywhere = true;
        $bread3Price->save();


        $milk1 = Product::firstOrNew(['identifier' => 'WEAKLY_MILK_PAID_WEEKLY']);
        $milk1->standalone = true;

        $milk1->service()->associate($mainService);
        $milk1->taxCategory()->associate($foodCategory);
        $milk1->subscriptionPlan()->associate($mainWeeklyPlan);
        $milk1->save();
        $milk1->translateOrNew('en')->name = 'Milk';
        $milk1->translateOrNew('de')->name = 'Milch';
        $milk1->translateOrNew('sk')->name = 'Mlieko';
        $milk1->translateOrNew('cz')->name = 'Mléko';
        $milk1->save();
        $milk1Price = Price::firstOrNew(['product_id' => $milk1->id]);
        $milk1Price->service()->associate($mainService);
        $milk1Price->amount = '0.75';
        $milk1Price->currency()->associate($euro);
        $milk1Price->everywhere = true;
        $milk1Price->save();

        $milk2 = Product::firstOrNew(['identifier' => 'WEAKLY_MILK_PAID_MONTHLY']);
        $milk2->standalone = true;

        $milk2->service()->associate($mainService);
        $milk2->taxCategory()->associate($foodCategory);
        $milk2->subscriptionPlan()->associate($mainMonthlyPlan);
        $milk2->save();
        $milk2->translateOrNew('en')->name = 'Milk';
        $milk2->translateOrNew('de')->name = 'Milch';
        $milk2->translateOrNew('sk')->name = 'Mlieko';
        $milk2->translateOrNew('cz')->name = 'Mléko';
        $milk2->save();
        $milk2Price = Price::firstOrNew(['product_id' => $milk2->id]);
        $milk2Price->service()->associate($mainService);
        $milk2Price->amount = '2.00';
        $milk2Price->currency()->associate($euro);
        $milk2Price->everywhere = true;
        $milk2Price->save();

        $milk3 = Product::firstOrNew(['identifier' => 'WEAKLY_MILK_PAID_YEARLY']);
        $milk3->standalone = true;

        $milk3->service()->associate($mainService);
        $milk3->taxCategory()->associate($foodCategory);
        $milk3->subscriptionPlan()->associate($mainYearlyPlan);
        $milk3->save();
        $milk3->translateOrNew('en')->name = 'Milk';
        $milk3->translateOrNew('de')->name = 'Milch';
        $milk3->translateOrNew('sk')->name = 'Mlieko';
        $milk3->translateOrNew('cz')->name = 'Mléko';
        $milk3->save();
        $milk3Price = Price::firstOrNew(['product_id' => $milk3->id]);
        $milk3Price->service()->associate($mainService);
        $milk3Price->amount = '20.00';
        $milk3Price->currency()->associate($euro);
        $milk3Price->everywhere = true;
        $milk3Price->save();


        $butter1 = Product::firstOrNew(['identifier' => 'WEAKLY_BUTTER_PAID_WEEKLY']);
        $butter1->standalone = true;

        $butter1->service()->associate($mainService);
        $butter1->taxCategory()->associate($foodCategory);
        $butter1->subscriptionPlan()->associate($mainWeeklyPlan);
        $butter1->save();
        $butter1->translateOrNew('en')->name = 'Butter';
        $butter1->translateOrNew('de')->name = 'Butter';
        $butter1->translateOrNew('sk')->name = 'Maslo';
        $butter1->translateOrNew('cz')->name = 'Máslo';
        $butter1->save();
        $butter1Price = Price::firstOrNew(['product_id' => $butter1->id]);
        $butter1Price->service()->associate($mainService);
        $butter1Price->amount = '1.20';
        $butter1Price->currency()->associate($euro);
        $butter1Price->everywhere = true;
        $butter1Price->save();

        $butter2 = Product::firstOrNew(['identifier' => 'WEAKLY_BUTTER_PAID_MONTHLY']);
        $butter2->standalone = true;

        $butter2->service()->associate($mainService);
        $butter2->taxCategory()->associate($foodCategory);
        $butter2->subscriptionPlan()->associate($mainMonthlyPlan);
        $butter2->save();
        $butter2->translateOrNew('en')->name = 'Butter';
        $butter2->translateOrNew('de')->name = 'Butter';
        $butter2->translateOrNew('sk')->name = 'Maslo';
        $butter2->translateOrNew('cz')->name = 'Máslo';
        $butter2->save();
        $butter2Price = Price::firstOrNew(['product_id' => $butter2->id]);
        $butter2Price->service()->associate($mainService);
        $butter2Price->amount = '4.00';
        $butter2Price->currency()->associate($euro);
        $butter2Price->everywhere = true;
        $butter2Price->save();

        $butter3 = Product::firstOrNew(['identifier' => 'WEAKLY_BUTTER_PAID_YEARLY']);
        $butter3->standalone = true;

        $butter3->service()->associate($mainService);
        $butter3->taxCategory()->associate($foodCategory);
        $butter3->subscriptionPlan()->associate($mainYearlyPlan);
        $butter3->save();
        $butter3->translateOrNew('en')->name = 'Butter';
        $butter3->translateOrNew('de')->name = 'Butter';
        $butter3->translateOrNew('sk')->name = 'Maslo';
        $butter3->translateOrNew('cz')->name = 'Máslo';
        $butter3->save();
        $butter3Price = Price::firstOrNew(['product_id' => $butter3->id]);
        $butter3Price->service()->associate($mainService);
        $butter3Price->amount = '40.00';
        $butter3Price->currency()->associate($euro);
        $butter3Price->everywhere = true;
        $butter3Price->save();

        /*
        $cheese = Product::firstOrNew(['identifier' => 'CHEESE']);
        $cheese->standalone = true;

        $cheese->service()->associate($mainService);
        $cheese->taxCategory()->associate($foodCategory);
        $cheese->save();
        $cheese->translateOrNew('en')->name = 'Cheese';
        $cheese->translateOrNew('de')->name = 'Käse';
        $cheese->translateOrNew('sk')->name = 'Syr';
        $cheese->translateOrNew('cz')->name = 'Sýr';
        $cheese->save();
        $cheesePrice = Price::firstOrNew(['product_id' => $cheese->id]);
        $cheesePrice->service()->associate($mainService);
        $cheesePrice->amount = '4.50';
        $cheesePrice->currency()->associate($euro);
        $cheesePrice->everywhere = true;
        $cheesePrice->save();

        $potatoes = Product::firstOrNew(['identifier' => 'POTATOES1KG']);
        $potatoes->standalone = true;

        $potatoes->service()->associate($mainService);
        $potatoes->taxCategory()->associate($foodCategory);
        $potatoes->save();
        $potatoes->translateOrNew('en')->name = 'Potatoes - 1 Kg';
        $potatoes->translateOrNew('de')->name = 'Erdaäpfel - 1 Kg';
        $potatoes->translateOrNew('sk')->name = 'Zemiaky - 1 Kg';
        $potatoes->translateOrNew('cz')->name = 'Brambory - 1 Kg';
        $potatoes->save();
        $potatoesPrice = Price::firstOrNew(['product_id' => $potatoes->id]);
        $potatoesPrice->service()->associate($mainService);
        $potatoesPrice->amount = '0.80';
        $potatoesPrice->currency()->associate($euro);
        $potatoesPrice->everywhere = true;
        $potatoesPrice->save();

        $rice = Product::firstOrNew(['identifier' => 'RICE1KG']);
        $rice->standalone = true;

        $rice->service()->associate($mainService);
        $rice->taxCategory()->associate($foodCategory);
        $rice->save();
        $rice->translateOrNew('en')->name = 'Rice - 1 Kg';
        $rice->translateOrNew('de')->name = 'Reis - 1 Kg';
        $rice->translateOrNew('sk')->name = 'Ryža - 1 Kg';
        $rice->translateOrNew('cz')->name = 'Rýže - 1 Kg';
        $rice->save();
        $ricePrice = Price::firstOrNew(['product_id' => $rice->id]);
        $ricePrice->service()->associate($mainService);
        $ricePrice->amount = '0.70';
        $ricePrice->currency()->associate($euro);
        $ricePrice->everywhere = true;
        $ricePrice->save();
        */
    }
}
