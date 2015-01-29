<?php namespace Subscribo\App\Seeder;

use Illuminate\Database\Seeder;
use Subscribo\App\Model\User;
use Subscribo\App\Model\UserToken;
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

        $server = new User();
        $server->username = 'frontend';
        $server->type = User::TYPE_SERVER;
        $server->save();
        $userFactory->addTokens($server, UserToken::TYPE_SUBSCRIBO_DIGEST);

        $administrator = $userFactory->create(['password' => 'administrator']);
        $administrator->username = 'administrator';
        $administrator->type = User::TYPE_ADMINISTRATOR;
        $administrator->save();

        $developer = $userFactory->create(['password' => 'developer']);
        $developer->username = 'developer';
        $developer->type = User::TYPE_ADMINISTRATOR;
        $userFactory->addTokens($developer);
        $developer->save();



    }

}