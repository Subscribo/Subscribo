<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Country;

class CountrySeeder extends Seeder {

    public function run()
    {
        $austria = Country::firstOrNew(['identifier' => 'AT']);
        $austria->officialName = 'Österreich';
        $austria->save();
        $austria->translateOrNew('en')->name = 'Austria';
        $austria->translateOrNew('de')->name = 'Österreich';
        $austria->translateOrNew('sk')->name = 'Rakúsko';
        $austria->countryUnion = 'EU';
        $austria->save();

        $germany = Country::firstOrNew(['identifier' => 'DE']);
        $germany->officialName = 'Deutschland';
        $germany->translateOrNew('en')->name = 'Germany';
        $germany->translateOrNew('de')->name = 'Deutschland';
        $germany->translateOrNew('sk')->name = 'Nemecko';
        $germany->countryUnion = 'EU';
        $germany->save();

        $slovakia = Country::firstOrNew(['identifier' => 'SK']);
        $slovakia->officialName = 'Slovensko';
        $slovakia->translateOrNew('en')->name = 'Slovakia';
        $slovakia->translateOrNew('de')->name = 'Slowakei';
        $slovakia->translateOrNew('sk')->name = 'Slovensko';
        $slovakia->countryUnion = 'EU';
    }
}
