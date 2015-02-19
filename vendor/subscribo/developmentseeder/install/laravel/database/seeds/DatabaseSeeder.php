<?php
/**
 * Taken from default Laravel (5.0) project - www.laravel.com - and modified
 *
 * @license MIT
 */
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{

    public function run()
    {
        Model::unguard();

        $this->run('Subscribo\\DevelopmentSeeder\\Seeds\\BasicSeeder');
    }
}
