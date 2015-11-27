<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Product;
use Subscribo\ModelCore\Models\Price;
use Subscribo\ModelCore\Models\Service;
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

        $product1 = Product::firstOrNew(['identifier' => 'Product1']);
        $product1->standalone = true;

        $product1->service()->associate($frontendService);
        $product1->taxCategory()->associate($standardTaxCategory);
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

        $bread = Product::firstOrNew(['identifier' => 'BREAD']);
        $bread->standalone = true;

        $bread->service()->associate($mainService);
        $bread->taxCategory()->associate($foodCategory);
        $bread->save();
        $bread->translateOrNew('en')->name = 'Bread';
        $bread->translateOrNew('de')->name = 'Brot';
        $bread->translateOrNew('sk')->name = 'Chlieb';
        $bread->translateOrNew('cz')->name = 'Chléb';
        $bread->save();
        $breadPrice = Price::firstOrNew(['product_id' => $bread->id]);
        $breadPrice->service()->associate($mainService);
        $breadPrice->amount = '1.00';
        $breadPrice->currency()->associate($euro);
        $breadPrice->everywhere = true;
        $breadPrice->save();

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
    }
}
