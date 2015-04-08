<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\TagGroup;
use Subscribo\ModelCore\Models\Tag;

class TagSeeder extends Seeder {

    public function run()
    {
        $generalTagGroup = TagGroup::firstOrCreate(['identifier' => 'GENERAL']);
        $generalTagGroup->name = 'General';
        $generalTagGroup->translateOrNew('sk')->name = 'Všeobecné';
        $generalTagGroup->translateOrNew('de')->name = 'Generell';
        $generalTagGroup->save();

        $testTag1 = Tag::firstOrNew(['identifier' => 'TEST_TAG1']);
        $testTag1->name = 'Testing tag 1';
        $testTag1->translateOrNew('de')->name = 'Tag zum testen 1';
        $testTag1->tagGroup()->associate($generalTagGroup);
        $testTag1->save();

        $testTag2 = Tag::firstOrNew(['identifier' => 'TEST_TAG2']);
        $testTag2->name = 'Testing tag 2';
        $testTag2->translateOrNew('sk')->name = 'Testovací tag 2';
        $testTag2->tagGroup()->associate($generalTagGroup);
        $testTag2->save();
    }
}
