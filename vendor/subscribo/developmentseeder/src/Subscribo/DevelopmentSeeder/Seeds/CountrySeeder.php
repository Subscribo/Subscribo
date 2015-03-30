<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Country;

class CountrySeeder extends Seeder {

    public function run()
    {
        $austria = Country::firstOrNew(['identifier' => 'AT']);
        $austria->save();
        $austria->officialName = 'Österreich';
        $austria->translateOrNew('en')->name = 'Austria';
        $austria->translateOrNew('de')->name = 'Österreich';
        $austria->countryUnion = 'EU';
        $austria->save();

        $germany = Country::firstOrNew(['identifier' => 'DE']);
        $germany->officialName = 'Deutschland';
        $germany->translateOrNew('en')->name = 'Germany';
        $germany->translateOrNew('de')->name = 'Deutschland';
        $germany->countryUnion = 'EU';
        $germany->save();
    }
}
