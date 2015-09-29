<?php

namespace Subscribo\DevelopmentSeeder\Tools;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EnvFileTools
 *
 * @package Subscribo\DevelopmentSeeder
 */
class EnvFileTools
{
    /**
     * @param string $key
     * @param string|int $value
     * @param array|string $fileNames
     * @param OutputInterface|null $output
     */
    public static function updateEnvFile($key, $value, $fileNames = '.env', OutputInterface $output = null)
    {
        $fileNames = is_array($fileNames) ? $fileNames : [$fileNames];
        foreach ($fileNames as $fileName) {
            $envFilePath = base_path($fileName);
            if (file_exists($envFilePath)) {
                $oldContent = file_get_contents($fileName);
                $count = 0;
                $newContent = preg_replace('/^([ \\t]*)'.$key.'=.*$/m', $key.'='.$value, $oldContent, 1, $count);
                if (empty($count)) {
                    $newContent = $oldContent."\n\n".$key.'='.$value."\n";
                }
                file_put_contents($fileName, $newContent);
                if ($output) {
                    $output->writeln(sprintf('Environment file %s updated', $fileName));
                }
            } else {
                if ($output) {
                    $output->writeln(sprintf('Environment file %s not found - skipped', $fileName));
                }
            }
        }
    }
}
