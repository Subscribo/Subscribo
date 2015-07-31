<?php

namespace Subscribo\ModelBase\Traits;

use RuntimeException;
use Subscribo\Support\Str;

/**
 * Trait HavingHashTrait Trait for models having (unique) hash
 * @package Subscribo\ModelBase
 */
trait HavingHashTrait
{
    /**
     * @param string $hash
     * @return static
     */
    public static function findByHash($hash)
    {
        $query = static::query()->where(['hash' => $hash]);

        return $query->first();
    }

    /**
     * @param int|bool $length
     * @param int|bool $attempts
     * @param bool $throwExceptionOnFailure Whether to throw exception on failure to find unused hash
     * @return string|null
     * @throws \RuntimeException
     */
    public static function getUnusedHash($length = true, $attempts = true, $throwExceptionOnFailure = true)
    {
        if (true === $length) {
            $length = static::getDefaultHashLength();
        }
        if (true === $attempts) {
            $attempts = static::getDefaultHashTryingAttempts();
        }
        $hash = Str::random($length);

        for ($i = 0; $i < $attempts; $i++) {
            $found = static::findByHash($hash);
            if (empty($found)) {

                return $hash;
            }
        }
        if ($throwExceptionOnFailure) {

            throw new RuntimeException('Unable to find unused hash');
        }

        return null;
    }

    /**
     * @param array $attributes
     * @param int|bool $hashLength
     * @param bool $throwExceptionOnFailureToFindHash Whether to throw exception on failure to find unused hash
     * @return static
     */
    public static function makeWithHash(array $attributes = [], $hashLength = true, $throwExceptionOnFailureToFindHash = true)
    {
        $instance = new static($attributes);
        $instance->addHash($hashLength, true, $throwExceptionOnFailureToFindHash);

        return $instance;
    }

    /**
     * @param array $attributes
     * @param int|bool $hashLength
     * @return static
     */
    public static function generateWithHash(array $attributes = [], $hashLength = true)
    {
        $instance = static::makeWithHash($attributes, $hashLength, true);
        $instance->save();

        return $instance;
    }

    /**
     * @param bool int|bool $length
     * @param bool int|bool $attempts
     * @param bool $throwExceptionOnFailure
     * @return $this
     */
    public function addHash($length = true, $attempts = true, $throwExceptionOnFailure = true)
    {
        $this->hash = static::getUnusedHash($length, $attempts, $throwExceptionOnFailure);

        return $this;
    }

    /**
     * @return int
     */
    protected static function getDefaultHashLength()
    {
        return 32;
    }

    /**
     * @return int
     */
    protected static function getDefaultHashTryingAttempts()
    {
        return 10;
    }
}
