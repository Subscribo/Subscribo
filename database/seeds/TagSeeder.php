<?php namespace Subscribo\App\Seeder;

use Illuminate\Database\Seeder;
use Model\TagGroup;
use Model\Tag;

class TagSeeder extends Seeder {

    public function run()
    {
        $generalTagGroup = TagGroup::firstOrCreate(['identifier' => 'GENERAL', 'name' => 'General']);
        $testTag1 = Tag::firstOrNew(['identifier' => 'TEST_TAG1']);
        $testTag1->name = 'Test tag 1';
        $testTag1->tagGroup()->associate($generalTagGroup);
        $testTag1->save();

        $testTag2 = Tag::firstOrNew(['identifier' => 'TEST_TAG2']);
        $testTag2->name = 'Test tag 2';
        $testTag2->tagGroup()->associate($generalTagGroup);
        $testTag2->save();


    }
}
