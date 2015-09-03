<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;

/**
 * Class BusinessSeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class BusinessSeeder extends Seeder
{
    public function run()
    {
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\TransactionGatewaySeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\TaxSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\TransactionGatewayConfigurationSeeder');
        $this->call('Subscribo\\DevelopmentSeeder\\Seeds\\ProductSeeder');
    }

}
