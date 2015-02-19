<?php namespace Subscribo\App\Seeder;

use Illuminate\Database\Seeder;
use Subscribo\App\Model\Country;

class CountrySeeder extends Seeder {

    public function run()
    {
        $austria = Country::firstOrNew(['identifier' => 'AT']);
        $austria->name = 'Österreich';
        $austria->englishName = 'Austria';
        $austria->germanName = 'Österreich';
        $austria->countryUnion = 'EU';
        $austria->save();

        $germany = Country::firstOrNew(['identifier' => 'DE']);
        $germany->name = 'Deutschland';
        $germany->englishName = 'Germany';
        $germany->germanName = 'Deutschland';
        $germany->countryUnion = 'EU';
        $germany->save();
    }
}
