<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\User;
use Subscribo\ModelCore\Models\UserToken;
use Subscribo\ModelCore\Models\Service;
use Subscribo\Auth\Factories\UserFactory;
use App;
use Subscribo\Support\Str;

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

        $superAdmin = $this->generateUser('admin', User::TYPE_SUPER_ADMIN);

        $service = Service::first();
        $server = new User();
        $server->username = 'frontend';
        $server->type = User::TYPE_SERVER;
        $server->service()->associate($service);
        $server->save();
        $userFactory->addTokens($server, UserToken::TYPE_SUBSCRIBO_DIGEST);

        $administrator = $this->generateUser('administrator');
        $userFactory->addTokens($administrator);
        $administrator->service()->associate($service);
        $administrator->save();

        $service2 = Service::where(['identifier' => 'MAIN'])->first();
        $developer = $this->generateUser('developer');
        $developer->service()->associate($service2);
        $userFactory->addTokens($developer);
        $developer->save();

        $anotherService = Service::where(['identifier' => 'ANOTHER'])->first();
        $anotherDeveloper = $this->generateUser('developer5');
        $anotherDeveloper->service()->associate($anotherService);
        $userFactory->addTokens($anotherDeveloper);
        $anotherDeveloper->save();
    }

    protected function generateUser($username, $type = USER::TYPE_ADMINISTRATOR)
    {
        /** @var \Subscribo\Auth\Factories\UserFactory $userFactory */
        $userFactory = App::make('Subscribo\\Auth\\Factories\\UserFactory');
        $password = Str::random();
        $user = $userFactory->create(['username' => $username, 'password' => $password, 'type' => $type]);
        $user->save();
        if ($this->command) {
            $this->command->getOutput()->writeln(sprintf('User %s : %s', $username, $password));
        }
        return $user;
    }

}
