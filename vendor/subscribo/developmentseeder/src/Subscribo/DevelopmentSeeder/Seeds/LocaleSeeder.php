<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Locale;

class LocaleSeeder extends Seeder {

    public function run()
    {
        $english = Locale::firstOrCreate([
            'identifier' => 'en',
            'type'          => Locale::TYPE_GENERIC,
            'native_name'  => 'English',
        ]);
        $english->translateOrNew('en')->name = 'English';
        $english->translateOrNew('de')->name = 'Englisch';
        $english->save();

        $americanEnglish = Locale::firstOrCreate([
            'identifier' => 'en_US',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'American English',
        ]);
        $americanEnglish->translateOrNew('en')->name = 'American English';
        $americanEnglish->translateOrNew('de')->name = 'Amerikanisches Englisch';
        $americanEnglish->save();

        $britishEnglish = Locale::firstOrCreate([
            'identifier' => 'en_UK',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'British English',
        ]);
        $britishEnglish->translateOrNew('en')->name = 'British English';
        $britishEnglish->translateOrNew('de')->name = 'Britisch Englisch';
        $britishEnglish->save();

        $german = Locale::firstOrCreate([
            'identifier' => 'de',
            'type'          => Locale::TYPE_GENERIC,
            'native_name'  => 'Deutsch',
        ]);
        $german->translateOrNew('en')->name = 'German';
        $german->translateOrNew('de')->name = 'Deutsch';
        $german->save();

        $austrianGerman = Locale::firstOrCreate([
            'identifier' => 'de_AT',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'Österreichisches Deutsch',
        ]);
        $austrianGerman->translateOrNew('en')->name = 'Austrian German';
        $austrianGerman->translateOrNew('de')->name = 'Österreichisches Deutsch';
        $austrianGerman->save();

        $germanyGerman = Locale::firstOrCreate([
            'identifier' => 'de_DE',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'Deutschland Deutsch',
        ]);
        $germanyGerman->translateOrNew('en')->name = 'Germany German';
        $germanyGerman->translateOrNew('de')->name = 'Deutschland Deutsch';
        $germanyGerman->save();

        $swissGerman = Locale::firstOrCreate([
            'identifier' => 'de_CH',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'SchwizerDütsch',
        ]);
        $swissGerman->translateOrNew('en')->name = 'Swiss German';
        $swissGerman->translateOrNew('de')->name = 'SchweizerDeutsch';
        $swissGerman->save();

        $slovak = Locale::firstOrCreate([
            'identifier' => 'sk',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'Slovenčina',
        ]);
        $slovak->translateOrNew('en')->name = 'Slovak';
        $slovak->translateOrNew('de')->name = 'Slowakisch';
        $slovak->save();

        $slovakiaSlovak = Locale::firstOrCreate([
            'identifier' => 'sk_SK',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'Slovenčina',
        ]);
        $slovakiaSlovak->translateOrNew('en')->name = 'Slovak';
        $slovakiaSlovak->translateOrNew('de')->name = 'Slowakisch';
        $slovakiaSlovak->save();

        $czech = Locale::firstOrCreate([
            'identifier' => 'cs',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'Čeština',
        ]);
        $czech->translateOrNew('en')->name = 'Czech';
        $czech->translateOrNew('de')->name = 'Tschechisch';
        $czech->save();

        $czechRepublicCzech = Locale::firstOrCreate([
            'identifier' => 'cs_CZ',
            'type'          => Locale::TYPE_STANDARD,
            'native_name'  => 'Čeština',
        ]);
        $czechRepublicCzech->translateOrNew('en')->name = 'Czech';
        $czechRepublicCzech->translateOrNew('de')->name = 'Tschechisch';
        $czechRepublicCzech->save();

        $frontendEnglish = Locale::firstOrCreate([
            'identifier' => 'en_US-FRONTEND',
            'type'          => Locale::TYPE_CUSTOMIZED,
            'native_name'  => 'English for Frontend',
        ]);
        $frontendEnglish->translateOrNew('en')->name = 'English for Frontend';
        $frontendEnglish->translateOrNew('de')->name = 'Englisch für Frontend';
        $frontendEnglish->save();

        $frontendGerman = Locale::firstOrCreate([
            'identifier' => 'de_AT-FRONTEND',
            'type'          => Locale::TYPE_CUSTOMIZED,
            'native_name'  => 'Deutsch für Frontend',
        ]);
        $frontendGerman->translateOrNew('en')->name = 'German for Frontend';
        $frontendGerman->translateOrNew('de')->name = 'Deutsch für Frontend';
        $frontendGerman->save();

        $frontendSlovak = Locale::firstOrCreate([
            'identifier' => 'sk_SK-FRONTEND',
            'type'          => Locale::TYPE_CUSTOMIZED,
            'native_name'  => 'Slovenčina pre Frontend',
        ]);
        $frontendSlovak->translateOrNew('en')->name = 'Slovak for Frontend';
        $frontendSlovak->translateOrNew('de')->name = 'Slowakisch für Frontend';
        $frontendSlovak->save();

        $britishEnglish->fallbackLocales()->attach($english, ['ordering' => 10]);
        $britishEnglish->fallbackLocales()->attach($americanEnglish, ['ordering' => 15]);
        $americanEnglish->fallbackLocales()->attach($english, ['ordering' => 10]);
        $americanEnglish->fallbackLocales()->attach($britishEnglish, ['ordering' => 15]);

        $german->fallbackLocales()->attach($english, ['ordering' => 100]);
        $germanyGerman->fallbackLocales()->attach($austrianGerman, ['ordering' => 5]);
        $germanyGerman->fallbackLocales()->attach($german, ['ordering' => 10]);
        $germanyGerman->fallbackLocales()->attach($english, ['ordering' => 100]);
        $austrianGerman->fallbackLocales()->attach($germanyGerman, ['ordering' => 5]);
        $austrianGerman->fallbackLocales()->attach($german, ['ordering' => 10]);
        $austrianGerman->fallbackLocales()->attach($english, ['ordering' => 100]);
        $swissGerman->fallbackLocales()->attach($germanyGerman, ['ordering' => 5]);
        $swissGerman->fallbackLocales()->attach($german, ['ordering' => 10]);
        $swissGerman->fallbackLocales()->attach($english, ['ordering' => 100]);

        $slovak->fallbackLocales()->attach($czech, ['ordering' => 20]);
        $slovak->fallbackLocales()->attach($english, ['ordering' => 100]);
        $slovakiaSlovak->fallbackLocales()->attach($slovak, ['ordering' => 10]);
        $slovakiaSlovak->fallbackLocales()->attach($czech, ['ordering' => 20]);
        $slovakiaSlovak->fallbackLocales()->attach($czechRepublicCzech, ['ordering' => 25]);
        $slovakiaSlovak->fallbackLocales()->attach($english, ['ordering' => 100]);

        $czech->fallbackLocales()->attach($slovak, ['ordering' => 20]);
        $czech->fallbackLocales()->attach($english, ['ordering' => 100]);
        $czechRepublicCzech->fallbackLocales()->attach($czech, ['ordering' => 10]);
        $czechRepublicCzech->fallbackLocales()->attach($slovak, ['ordering' => 20]);
        $czechRepublicCzech->fallbackLocales()->attach($slovakiaSlovak, ['ordering' => 25]);
        $czechRepublicCzech->fallbackLocales()->attach($english, ['ordering' => 100]);

        $frontendEnglish->fallbackLocales()->attach($americanEnglish, ['ordering' => 1]);
        $frontendEnglish->fallbackLocales()->attach($english, ['ordering' => 10]);
        $frontendEnglish->fallbackLocales()->attach($britishEnglish, ['ordering' => 15]);
        $frontendEnglish->fallbackLocales()->attach($frontendGerman, ['ordering' => 201]);
        $frontendEnglish->fallbackLocales()->attach($frontendSlovak, ['ordering' => 202]);


        $frontendGerman->fallbackLocales()->attach($austrianGerman, ['ordering' => 1]);
        $frontendGerman->fallbackLocales()->attach($german, ['ordering' => 10]);
        $frontendGerman->fallbackLocales()->attach($germanyGerman, ['ordering' => 15]);
        $frontendGerman->fallbackLocales()->attach($frontendEnglish, ['ordering' => 80]);
        $frontendGerman->fallbackLocales()->attach($english, ['ordering' => 100]);
        $frontendGerman->fallbackLocales()->attach($frontendSlovak, ['ordering' => 202]);

        $frontendSlovak->fallbackLocales()->attach($slovakiaSlovak, ['ordering' => 1]);
        $frontendSlovak->fallbackLocales()->attach($slovak, ['ordering' => 2]);
        $frontendSlovak->fallbackLocales()->attach($czech, ['ordering' => 20]);
        $frontendSlovak->fallbackLocales()->attach($czechRepublicCzech, ['ordering' => 25]);
        $frontendSlovak->fallbackLocales()->attach($frontendEnglish, ['ordering' => 80]);
        $frontendSlovak->fallbackLocales()->attach($english, ['ordering' => 100]);
        $frontendSlovak->fallbackLocales()->attach($frontendGerman, ['ordering' => 201]);

    }
}
