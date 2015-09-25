<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\User;
use Subscribo\ModelCore\Models\UserToken;
use Subscribo\ModelCore\Models\Service;
use Subscribo\Auth\Factories\UserFactory;
use Subscribo\DevelopmentSeeder\Tools\EnvFileTools;
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
        $frontendSystemUser = new User();
        $frontendSystemUser->username = 'frontend';
        $frontendSystemUser->type = User::TYPE_SERVER;
        $frontendSystemUser->service()->associate($service);
        $frontendSystemUser->save();
        $tokens = $userFactory->addTokens($frontendSystemUser, UserToken::TYPE_SUBSCRIBO_DIGEST);
        /** @var UserToken $token */
        $token = reset($tokens);
        EnvFileTools::updateEnvFile(
            'SUBSCRIBO_REST_CLIENT_TOKEN_RING',
            $token->tokenRing,
            '.env.frontend',
            $this->command->getOutput()
        );


        $administrator = $this->generateUser('administrator');
        $userFactory->addTokens($administrator);
        $administrator->service()->associate($service);
        $administrator->save();

        $mainService = Service::where(['identifier' => 'MAIN'])->first();
        $developer = $this->generateUser('developer');
        $developer->service()->associate($mainService);
        $userFactory->addTokens($developer);
        $developer->save();
        $mainSystemUser = new User();
        $mainSystemUser->username = 'main';
        $mainSystemUser->type = User::TYPE_SERVER;
        $mainSystemUser->service()->associate($mainService);
        $mainSystemUser->save();
        $mainTokens = $userFactory->addTokens($mainSystemUser, UserToken::TYPE_SUBSCRIBO_DIGEST);
        /** @var UserToken $token */
        $tokenForMain = reset($mainTokens);
        EnvFileTools::updateEnvFile(
            'SUBSCRIBO_REST_CLIENT_TOKEN_RING',
            $tokenForMain->tokenRing,
            ['.env', '.env.commandline'],
            $this->command->getOutput()
        );

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
