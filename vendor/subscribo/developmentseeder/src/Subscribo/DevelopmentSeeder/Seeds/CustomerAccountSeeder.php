<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Subscribo\Api1\Factories\AccountFactory;
use Subscribo\ModelCore\Models\Service;
use Illuminate\Support\Str;


class CustomerAccountSeeder extends Seeder
{
    public function run()
    {
        $testService = Service::query()->where(['identifier' => 'FRONTEND'])->first();
        $test2Service = Service::firstOrCreate(['identifier' => 'MAIN']);
        $test3Service = Service::firstOrCreate(['identifier' => 'TEST3']);
        $anotherService = Service::firstOrCreate(['identifier' => 'ANOTHER']);

        $this->generateCustomer($testService, 'test1@subscribo.io', 'First Tester');
        $this->generateCustomer($testService, 'test2@subscribo.io');
        for ($i = 1; $i < 10; $i++) {
            $this->generateCustomer($testService);
        }
        for ($i = 1; $i < 8; $i++) {
            $this->generateCustomer($test2Service);
        }
        for ($i = 1; $i < 3; $i++) {
            $this->generateCustomer($test3Service);
        }
        for ($i = 1; $i < 3; $i++) {
            $this->generateCustomer($anotherService);
        }
    }

    public function generateCustomer(Service $service, $email = null, $name = null)
    {
        /**  @var $accountFactory AccountFactory */
        $accountFactory = $this->container->make('Subscribo\\Api1\\Factories\\AccountFactory');

        $countries = $service->availableCountries;
        $country = $countries->random();
        $locales = [
            'AT' => 'de_AT',
            'DE' => 'de_DE',
            'SK' => 'sk_SK',
            'CZ' => 'cs_CZ',
            'GB' => 'en_GB',
            'US' => 'en_US',
        ];
        $locale = $locales[$country->identifier];
        $specialLastName = (($locale === 'cs_CZ') or ($locale === 'sk_SK'));

        $faker = Factory::create($locale);

        if ($email) {
            $data = [
                'email' => $email,
                'name' => $name,
            ];
        } else {
            $male = $faker->boolean();
            $data = [
                'first_name' => $male ? $faker->firstNameMale : $faker->firstNameFemale,
                'last_name' => $specialLastName
                    ? ($male ? $faker->lastNameMale : $faker->lastNameFemale)
                    : $faker->lastName,
                'gender' => $male ? 'man' : 'woman',
            ];
            $data['email'] = iconv('UTF-8', 'ASCII//IGNORE', $data['last_name']).'@subscribo.io';
        }
        $data['password'] = Str::random(10);
        $data['street'] = $faker->streetAddress;
        $data['city'] = $faker->city;
        $data['post_code'] = $faker->postcode;
        $data['phone'] = $faker->optional()->phoneNumber;
        $data['mobile'] = $faker->optional()->phoneNumber;
        $data['delivery_information'] = $faker->optional()->text;


        $data['country'] = $country->identifier;

        $result = $accountFactory->register($data, $service->id, '');

        if ($this->command) {
            $this->command->getOutput()->writeln(sprintf('[Service: %s] Customer %s : %s', $service->name, $data['email'], $data['password']));
        }
        return $result;
    }
}
