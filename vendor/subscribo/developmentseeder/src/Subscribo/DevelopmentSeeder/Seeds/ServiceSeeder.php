<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\ServicePool;
use Subscribo\ModelCore\Models\Language;

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

        $test2Service = Service::firstOrNew(['identifier' => 'TEST2']);
        $test2Service->defaultLanguage()->associate($american);
        $test2Service->save();
        $test2Service->availableLanguages()->attach($american);
        $test2Service->availableLanguages()->attach($british);

        $test3Service = Service::firstOrNew(['identifier' => 'TEST3']);
        $test3Service->defaultLanguage()->associate($american);
        $test3Service->save();
        $test3Service->availableLanguages()->attach($american);
        $test3Service->availableLanguages()->attach($british);

        $anotherService = Service::firstOrNew(['identifier' => 'ANOTHER']);
        $anotherService->defaultLanguage()->associate($american);
        $anotherService->save();
        $anotherService->availableLanguages()->attach($american);
        $anotherService->availableLanguages()->attach($british);

        $servicePool2 = ServicePool::firstOrCreate(['identifier' => 'POOL2']);
        $servicePool2->services()->attach($testService);
        $servicePool2->services()->attach($test2Service);
        $servicePool2->save();

        $servicePool3 = ServicePool::firstOrCreate(['identifier' => 'POOL3']);
        $servicePool3->services()->attach($testService);
        $servicePool3->services()->attach($test3Service);
        $servicePool3->save();

    }

}
