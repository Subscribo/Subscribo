<?php

namespace Subscribo\DevelopmentSeeder\Commands;

use Illuminate\Console\Command;
use Subscribo\Support\Str;
use Subscribo\DevelopmentSeeder\Tools\EnvFileTools;

class SubscriboCommonSecretGenerate extends Command
{
    protected $signature = "subscribo-common-secret:generate";

    protected $description = "Generate and sets in .env files common secret";

    public function handle()
    {
        $newCommonSecret = Str::random(32);
        $envFiles = ['.env', '.env.frontend'];
        foreach ($envFiles as $envFile) {
            EnvFileTools::updateEnvFile('SUBSCRIBO_COMMON_SECRET', $newCommonSecret, $envFile, $this->getOutput());
        }
    }
}
