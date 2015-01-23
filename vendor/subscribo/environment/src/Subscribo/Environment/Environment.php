<?php namespace Subscribo\Environment;

use Subscribo\Environment\Exceptions\AttemptToChangeEnvironmentException;
use Subscribo\Environment\Exceptions\AttemptToSetEnvironmentAgainException;

/**
 * Class Environment
 *
 * @package Subscribo\Environment
 */
class Environment implements EnvironmentInterface {

    const MODE_ALLOW_ENVIRONMENT_CHANGE = 'MODE_ALLOW_ENVIRONMENT_CHANGE';
    const MODE_ALLOW_SETTING_THE_SAME_ENVIRONMENT = 'MODE_ALLOW_SETTING_THE_SAME_ENVIRONMENT';
    const MODE_DISALLOW_REPEATED_SETTING_OF_ENVIRONMENT = 'MODE_DISALLOW_REPEATED_SETTING_OF_ENVIRONMENT';


    /**
     * @var null|string
     */
    protected $_environment = null;


    /**
     * @return string
     */
    public function detectEnvironment()
    {
        $environment = getenv('SUBSCRIBO_ENV');
        if ($environment) {
            return $environment;
        }
        if (array_key_exists('SUBSCRIBO_ENV', $_ENV)) {
            return $_ENV['SUBSCRIBO_ENV'];
        }
        if (array_key_exists('SUBSCRIBO_ENV', $_SERVER)) {
            return $_SERVER['SUBSCRIBO_ENV'];
        }
        return self::PRODUCTION;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        if (is_null($this->_environment)) {
            $this->_environment = $this->detectEnvironment();
        }
        return $this->_environment;
    }


    /**
     * @param string $environment Possible values: self::PRODUCTION, self::DEVELOPMENT, self::STAGING, self::TESTING or any string
     * @param string $mode Either self::MODE_ALLOW_ENVIRONMENT_CHANGE or self::MODE_ALLOW_SETTING_THE_SAME_ENVIRONMENT or self::MODE_DISALLOW_REPEATED_SETTING_OF_ENVIRONMENT (default)
     * @return $this
     * @throws Exceptions\AttemptToChangeEnvironmentException
     * @throws Exceptions\AttemptToSetEnvironmentAgainException
     */
    public function setEnvironment($environment, $mode = self::MODE_DISALLOW_REPEATED_SETTING_OF_ENVIRONMENT)
    {
        if ( ! is_null($this->_environment)) {
            // Allow only 3 possibilities for $mode
            if ((self::MODE_ALLOW_ENVIRONMENT_CHANGE !== $mode)
                and (self::MODE_ALLOW_SETTING_THE_SAME_ENVIRONMENT !== $mode)) {
                $mode = self::MODE_DISALLOW_REPEATED_SETTING_OF_ENVIRONMENT;
            }
            if ($this->_environment === $environment) {
                if (self::MODE_DISALLOW_REPEATED_SETTING_OF_ENVIRONMENT === $mode)
                {
                    throw new AttemptToSetEnvironmentAgainException();
                }
            } else {
                if (self::MODE_ALLOW_ENVIRONMENT_CHANGE !== $mode) {
                    throw new AttemptToChangeEnvironmentException();
                }
            }
        }
        $this->_environment = $environment;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetEnvironment()
    {
        $this->_environment = null;
        return $this;
    }
}
