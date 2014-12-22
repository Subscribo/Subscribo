<?php namespace Subscribo\Config;

use Subscribo\Environment\EnvironmentInterface;

class Config {

    /**
     * @var null|\Subscribo\Environment\EnvironmentInterface
     */
    protected $environmentInstance = null;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environmentInstance = $environment;
    }
}
