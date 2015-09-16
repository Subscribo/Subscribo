<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\TaxCategory;
use Subscribo\ModelCore\Models\TaxGroup;
use Subscribo\ModelCore\Models\Country;

/**
 * Class TaxSeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class TaxSeeder extends Seeder
{
    public function run()
    {
        $noTaxCategory = TaxCategory::firstOrNew(['identifier' => 'FREE']);
        $noTaxCategory->translateOrNew('en')->name = 'Free from tax';
        $noTaxCategory->translateOrNew('en')->shortName = 'O';
        $noTaxCategory->description = 'For marking items for which tax is not paid everywhere';
        $noTaxCategory->save();

        $noTaxGroup = TaxGroup::firstOrNew(['tax_category_id' => $noTaxCategory->id]);
        $noTaxGroup->taxPercent = 0;
        $noTaxGroup->taxType = 'none';

        $standardTaxCategory = TaxCategory::firstOrNew(['identifier' => 'STANDARD']);
        $standardTaxCategory->translateOrNew('en')->name = 'Standard tax';
        $standardTaxCategory->translateOrNew('en')->shortName = 'A';
        $standardTaxCategory->description = 'For marking items for which no special rules apply';
        $standardTaxCategory->isDefault = true;
        $standardTaxCategory->save();

        $austria = Country::firstOrCreate(['identifier' => 'AT']);
        $germany = Country::firstOrCreate(['identifier' => 'DE']);
        $slovakia = Country::firstOrCreate(['identifier' => 'SK']);
        $czechRepublic = Country::firstOrCreate(['identifier' => 'CZ']);
        $unitedKingdom = Country::firstOrCreate(['identifier' => 'GB']);
        $unitedStates = Country::firstOrCreate(['identifier' => 'US']);

        $standardGroupAustria = TaxGroup::firstOrCreate([
            'country_id' => $austria->id,
            'tax_category_id' => $standardTaxCategory->id,
        ]);
        $standardGroupAustria->taxPercent = '20';
        $standardGroupAustria->save();

        $standardGroupGermany = TaxGroup::firstOrCreate([
            'country_id' => $germany->id,
            'tax_category_id' => $standardTaxCategory->id,
        ]);
        $standardGroupGermany->taxPercent = '19';
        $standardGroupGermany->save();

        $standardGroupSlovakia = TaxGroup::firstOrCreate([
            'country_id' => $slovakia->id,
            'tax_category_id' => $standardTaxCategory->id,
        ]);
        $standardGroupSlovakia->taxPercent = '20';
        $standardGroupSlovakia->save();

        $foodCategory = TaxCategory::firstOrNew(['identifier' => 'FOOD']);
        $foodCategory->translateOrNew('en')->name = 'Food (non-luxury)';
        $foodCategory->translateOrNew('en')->shortName = 'B';
        $foodCategory->translateOrNew('de')->name = 'Lebensmittel (nicht luxuriös)';
        $foodCategory->translateOrNew('de')->shortName = 'B';
        $foodCategory->translateOrNew('sk')->name = 'Potraviny / Jedlo (nie luxusné)';
        $foodCategory->translateOrNew('sk')->shortName = 'B';
        $foodCategory->description = 'Reduced tax for food items (with exception of luxury food)';
        $foodCategory->save();

        $foodGroupAustria = TaxGroup::firstOrCreate([
            'country_id' => $austria->id,
            'tax_category_id' => $foodCategory->id,
        ]);
        $foodGroupAustria->taxPercent = '10';
        $foodGroupAustria->save();

        $foodGroupGermany = TaxGroup::firstOrCreate([
            'country_id' => $germany->id,
            'tax_category_id' => $foodCategory->id,
        ]);
        $foodGroupGermany->taxPercent = '7';
        $foodGroupGermany->save();

        $foodGroupSlovakia = TaxGroup::firstOrCreate([
            'country_id' => $slovakia->id,
            'tax_category_id' => $foodCategory->id,
        ]);
        $foodGroupSlovakia->taxPercent = '10';
        $foodGroupSlovakia->save();
    }
}
