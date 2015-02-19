<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;

class BasicSeeder extends Seeder {

    public function run()
    {
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\LanguageSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\CountrySeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\ServiceSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\TagSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\UserSeeder');

    }
}
