<?php namespace Subscribo\Environment\Exceptions;

use Subscribo\Environment\Exceptions\Exception;

/**
 * Class AttemptToChangeEnvironmentException
 * Exception to be thrown, when a not allowed attempt to set existing environment to the same value had been made
 *
 * @package Subscribo\Environment
 */
class AttemptToSetEnvironmentAgainException extends Exception {}
