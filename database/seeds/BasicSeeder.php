<?php namespace Subscribo\App\Seeder;

use Illuminate\Database\Seeder;

class BasicSeeder extends Seeder {

    public function run()
    {
        $this->call('Subscribo\\App\\Seeder\\LanguageSeeder');
        $this->call('Subscribo\\App\\Seeder\\CountrySeeder');
        $this->call('Subscribo\\App\\Seeder\\ServiceSeeder');
        $this->call('Subscribo\\App\\Seeder\\TagSeeder');
        $this->call('Subscribo\\App\\Seeder\\UserSeeder');

    }
}
