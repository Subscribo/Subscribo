<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\User;
use Subscribo\ModelCore\Models\UserToken;
use Subscribo\ModelCore\Models\Service;
use Subscribo\Auth\Factories\UserFactory;
use App;

class UserSeeder extends Seeder
{
    public function run()
    {
        /** @var \Subscribo\Auth\Factories\UserFactory $userFactory */
        $userFactory = App::make('Subscribo\\Auth\\Factories\\UserFactory');
        $guest = new User();
        $guest->username = 'guest';
        $guest->password = 'Guest does not have password';
        $guest->type = User::TYPE_GUEST;
        $guest->save();

        $superAdmin = $userFactory->create(['password' => 'admin']);
        $superAdmin->username = 'admin';
        $superAdmin->type = User::TYPE_SUPER_ADMIN;
        $superAdmin->save();

        $service = Service::first();
        $server = new User();
        $server->username = 'frontend';
        $server->type = User::TYPE_SERVER;
        $server->service()->associate($service);
        $server->save();
        $userFactory->addTokens($server, UserToken::TYPE_SUBSCRIBO_DIGEST);

        $administrator = $userFactory->create(['password' => 'administrator']);
        $administrator->username = 'administrator';
        $administrator->type = User::TYPE_ADMINISTRATOR;
        $userFactory->addTokens($administrator);
        $administrator->service()->associate($service);
        $administrator->save();

        $service2 = Service::firstByAttributes(['identifier' => 'TEST2']);
        $developer = $userFactory->create(['password' => 'developer']);
        $developer->username = 'developer';
        $developer->type = User::TYPE_ADMINISTRATOR;
        $developer->service()->associate($service2);
        $userFactory->addTokens($developer);
        $developer->save();

        $anotherService = Service::firstByAttributes(['identifier' => 'ANOTHER']);
        $anotherDeveloper = $userFactory->create(['password' => 'developer']);
        $anotherDeveloper->username = 'developer5';
        $anotherDeveloper->type = User::TYPE_ADMINISTRATOR;
        $anotherDeveloper->service()->associate($anotherService);
        $userFactory->addTokens($anotherDeveloper);
        $anotherDeveloper->save();




    }

}