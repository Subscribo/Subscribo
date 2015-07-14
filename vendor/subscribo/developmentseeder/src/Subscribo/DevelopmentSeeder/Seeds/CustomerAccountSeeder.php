<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\Api1\Factories\AccountFactory;
use Subscribo\ModelCore\Models\Service;
use Illuminate\Support\Str;


class CustomerAccountSeeder extends Seeder
{
    public function run()
    {
        $testService = Service::query()->where(['identifier' => 'FRONTEND'])->first();
        $this->generateCustomer($testService, 'test1@subscribo.io', 'First Tester');
        $this->generateCustomer($testService, 'test2@subscribo.io');
    }

    public function generateCustomer($service, $email, $name = null)
    {
        /**  @var $accountFactory AccountFactory */
        $accountFactory = $this->container->make('Subscribo\\Api1\\Factories\\AccountFactory');
        $password = Str::random(10);
        $result = $accountFactory->register(['name' => $name, 'email' => $email, 'password' => $password], $service->id, '');

        if ($this->command) {
            $this->command->getOutput()->writeln(sprintf('Customer %s %s : %s', $name, $email, $password));
        }
        return $result;
    }
}
