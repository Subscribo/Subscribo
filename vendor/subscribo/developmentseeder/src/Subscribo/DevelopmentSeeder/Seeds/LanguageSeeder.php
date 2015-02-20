<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Language;

class LanguageSeeder extends Seeder {

    public function run()
    {
        $american = Language::firstOrCreate([
            'identifier' => 'EN_US',
            'english_name'  => 'American English',
            'german_name'   => 'Amerikanisches Englisch',
            'native_name'  => 'American English',
        ]);
        $british = Language::firstOrCreate([
            'identifier' => 'EN_UK',
            'english_name'  => 'British English',
            'german_name'   => 'Britisch Englisch',
            'native_name'  => 'British English',
        ]);
        $british->fallbackLanguage()->associate($american)->save();
    }
}
