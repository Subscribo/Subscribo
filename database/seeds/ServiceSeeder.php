<?php namespace Subscribo\App\Seeder;

use Illuminate\Database\Seeder;
use Model\Service;
use Model\Language;

class ServiceSeeder extends Seeder {

    public function run()
    {
        $american = Language::firstOrNew(['identifier' => 'EN_US']);
        $british = Language::firstOrNew(['identifier' => 'EN_UK']);
        $testService = Service::firstOrNew(['identifier' => 'TEST']);
        $testService->defaultLanguage()->associate($american);
        $testService->save();
        $testService->availableLanguages()->attach($american);
        $testService->availableLanguages()->attach($british);


    }

}
