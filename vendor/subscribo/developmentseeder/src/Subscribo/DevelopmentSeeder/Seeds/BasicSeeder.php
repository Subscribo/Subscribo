<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;

/**
 * Class BasicSeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class BasicSeeder extends Seeder
{
    public function run()
    {
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\LocaleSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\CountrySeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\ServiceSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\TagSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\UserSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\CustomerAccountSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\BusinessSeeder');
    }
}
