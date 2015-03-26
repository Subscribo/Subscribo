<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Locale;

class LocaleSeeder extends Seeder {

    public function run()
    {
        $english = Locale::firstOrCreate([
            'identifier' => 'en',
            'type'          => Locale::TYPE_GENERIC,
            'english_name'  => 'English',
            'german_name'   => 'Englisch',
            'native_name'  => 'English',
        ]);
        $americanEnglish = Locale::firstOrCreate([
            'identifier' => 'en_US',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'American English',
            'german_name'   => 'Amerikanisches Englisch',
            'native_name'  => 'American English',
        ]);
        $britishEnglish = Locale::firstOrCreate([
            'identifier' => 'en_UK',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'British English',
            'german_name'   => 'Britisch Englisch',
            'native_name'  => 'British English',
        ]);
        $german = Locale::firstOrCreate([
            'identifier' => 'de',
            'type'          => Locale::TYPE_GENERIC,
            'english_name'  => 'German',
            'german_name'   => 'Deutsch',
            'native_name'  => 'Deutsch',
        ]);
        $austrianGerman = Locale::firstOrCreate([
            'identifier' => 'de_AT',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'Austrian German',
            'german_name'   => 'Österreichisches Deutsch',
            'native_name'  => 'Österreichisches Deutsch',
        ]);
        $germanyGerman = Locale::firstOrCreate([
            'identifier' => 'de_DE',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'Germany German',
            'german_name'   => 'Deutschland Deutsch',
            'native_name'  => 'Deutschland Deutsch',
        ]);
        $swissGerman = Locale::firstOrCreate([
            'identifier' => 'de_CH',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'Swiss German',
            'german_name'   => 'SchweizerDeutsch',
            'native_name'  => 'SchwizerDütsch',
        ]);
        $slovak = Locale::firstOrCreate([
            'identifier' => 'sk',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'Slovak',
            'german_name'   => 'Slowakisch',
            'native_name'  => 'Slovenčina',
        ]);
        $slovakiaSlovak = Locale::firstOrCreate([
            'identifier' => 'sk_SK',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'Slovak',
            'german_name'   => 'Slowakisch',
            'native_name'  => 'Slovenčina',
        ]);
        $czech = Locale::firstOrCreate([
            'identifier' => 'cs',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'Czech',
            'german_name'   => 'Tschechisch',
            'native_name'  => 'Čeština',
        ]);
        $czechRepublicCzech = Locale::firstOrCreate([
            'identifier' => 'cs_CZ',
            'type'          => Locale::TYPE_STANDARD,
            'english_name'  => 'Czech',
            'german_name'   => 'Tschechisch',
            'native_name'  => 'Čeština',
        ]);

        $frontendEnglish = Locale::firstOrCreate([
            'identifier' => 'en_US-FRONTEND',
            'type'          => Locale::TYPE_CUSTOMIZED,
            'english_name'  => 'English for Frontend',
            'german_name'   => 'Englisch für Frontend',
            'native_name'  => 'English for Frontend',
        ]);
        $frontendGerman = Locale::firstOrCreate([
            'identifier' => 'de_AT-FRONTEND',
            'type'          => Locale::TYPE_CUSTOMIZED,
            'english_name'  => 'German for Frontend',
            'german_name'   => 'Deutsch für Frontend',
            'native_name'  => 'Deutsch für Frontend',
        ]);
        $frontendSlovak = Locale::firstOrCreate([
            'identifier' => 'sk_SK-FRONTEND',
            'type'          => Locale::TYPE_CUSTOMIZED,
            'english_name'  => 'Slovak for Frontend',
            'german_name'   => 'Slowakisch für Frontend',
            'native_name'  => 'Slovenčina pre Frontend',
        ]);
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
