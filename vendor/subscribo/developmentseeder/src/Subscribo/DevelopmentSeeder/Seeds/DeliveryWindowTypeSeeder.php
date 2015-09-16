<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\DeliveryWindowType;

/**
 * Class DeliveryWindowTypeSeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class DeliveryWindowTypeSeeder extends Seeder
{
    public function run()
    {
        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();
        $mainService = Service::query()->where(['identifier' => 'MAIN'])->first();

        $frontendTuesday = DeliveryWindowType::firstOrCreate(['identifier' => 'TUESDAY', 'service_id' => $frontendService->id]);
        $frontendTuesday->identifier = 'TUESDAY';
        $frontendTuesday->service()->associate($frontendService);
        $frontendTuesday->start = 'Tuesday 8:00';
        $frontendTuesday->end = 'Tuesday 18:00';
        $frontendTuesday->isUsual = true;
        $frontendTuesday->name = 'Tuesday';
        $frontendTuesday->save();

        $mainMonday = DeliveryWindowType::firstOrCreate(['identifier' => 'MONDAY_AFTERNOON', 'service_id' => $mainService->id]);
        $mainMonday->identifier = 'MONDAY_AFTERNOON';
        $mainMonday->service()->associate($mainService);
        $mainMonday->start = 'Monday 15:00';
        $mainMonday->end = 'Monday 19:00';
        $mainMonday->isUsual = true;
        $mainMonday->translateOrNew('en')->name = 'Monday afternoon';
        $mainMonday->translateOrNew('de')->name = 'Montag Nachmittag';
        $mainMonday->translateOrNew('cz')->name = 'Pondělí odpoledne';
        $mainMonday->translateOrNew('sk')->name = 'Pondelok odpoludnia';
        $mainMonday->save();

        $mainWednesday = DeliveryWindowType::firstOrCreate(['identifier' => 'WEDNESDAY_MORNING', 'service_id' => $mainService->id]);
        $mainWednesday->identifier = 'WEDNESDAY_MORNING';
        $mainWednesday->service()->associate($mainService);
        $mainWednesday->start = 'Wednesday 8:00';
        $mainWednesday->end = 'Wednesday 11:00';
        $mainWednesday->isUsual = true;
        $mainWednesday->translateOrNew('en')->name = 'Wednesday morning';
        $mainWednesday->save();
    }
}
