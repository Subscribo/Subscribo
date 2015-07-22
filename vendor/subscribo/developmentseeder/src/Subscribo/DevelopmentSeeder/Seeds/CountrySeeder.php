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
        $austria->translateOrNew('cs')->name = 'Rakousko';
        $austria->countryUnion = 'EU';
        $austria->save();

        $germany = Country::firstOrNew(['identifier' => 'DE']);
        $germany->officialName = 'Deutschland';
        $germany->translateOrNew('en')->name = 'Germany';
        $germany->translateOrNew('de')->name = 'Deutschland';
        $germany->translateOrNew('sk')->name = 'Nemecko';
        $germany->translateOrNew('cs')->name = 'Německo';
        $germany->countryUnion = 'EU';
        $germany->save();

        $slovakia = Country::firstOrNew(['identifier' => 'SK']);
        $slovakia->officialName = 'Slovensko';
        $slovakia->translateOrNew('en')->name = 'Slovakia';
        $slovakia->translateOrNew('de')->name = 'Slowakei';
        $slovakia->translateOrNew('sk')->name = 'Slovensko';
        $slovakia->translateOrNew('cs')->name = 'Slovensko';
        $slovakia->countryUnion = 'EU';
        $slovakia->save();

        $czechRepublic = Country::firstOrNew(['identifier' => 'CZ']);
        $czechRepublic->officialName = 'Česká republika';
        $czechRepublic->translateOrNew('en')->name = 'Czech Republic';
        $czechRepublic->translateOrNew('de')->name = 'Tschechische Republik';
        $czechRepublic->translateOrNew('sk')->name = 'Česká republika';
        $czechRepublic->translateOrNew('cs')->name = 'Česká republika';
        $czechRepublic->countryUnion = 'EU';
        $czechRepublic->save();

        $unitedKingdom = Country::firstOrNew(['identifier' => 'GB']);
        $unitedKingdom->officialName = 'United Kingdom of Great Britain and Northern Ireland';
        $unitedKingdom->translateOrNew('en')->name = 'United Kingdom';
        $unitedKingdom->translateOrNew('de')->name = 'Vereingte Königreich';
        $unitedKingdom->translateOrNew('sk')->name = 'Spojené Kráľovstvo';
        $unitedKingdom->translateOrNew('cs')->name = 'Spojené Království';
        $unitedKingdom->countryUnion = 'EU';
        $unitedKingdom->save();

        $unitedStates = Country::firstOrNew(['identifier' => 'US']);
        $unitedStates->officialName = 'United States of America';
        $unitedStates->translateOrNew('en')->name = 'United States';
        $unitedStates->translateOrNew('de')->name = 'Vereinigten Staaten';
        $unitedStates->translateOrNew('sk')->name = 'Spojené státy';
        $unitedStates->translateOrNew('cs')->name = 'Spojené štáty';
        $unitedStates->save();
    }
}
