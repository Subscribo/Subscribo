<?php namespace Subscribo\Environment;

interface EnvironmentInterface
{

    const PRODUCTION = 'production';
    const DEVELOPMENT = 'development';
    const STAGING = 'staging';
    const TESTING = 'testing';

    /**
     * @return string
     */
    public function getEnvironment();
}
