<?php

namespace Subscribo\ModelBase\Traits;

use RuntimeException;
use Subscribo\Support\Str;
use Webpatser\Uuid\Uuid;

/**
 * Trait HasHashTrait Trait for models which has a(n) (unique) hash
 *
 * @package Subscribo\ModelBase
 */
trait HasHashTrait
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
     * @param int|bool $length false for UUID, true for getting it from getDefaultHashLength()
     * @param int|bool $attempts
     * @param bool $throwExceptionOnFailure Whether to throw exception on failure to find unused hash
     * @return string|null
     * @throws \RuntimeException
     */
    public static function getUnusedHash($length = false, $attempts = true, $throwExceptionOnFailure = true)
    {
        if (true === $length) {
            $length = static::getDefaultHashLength();
        }
        if (true === $attempts) {
            $attempts = static::getDefaultHashTryingAttempts();
        }
        if (is_int($length)) {
            $hash = Str::random($length);
        } else {
            $hash = (string) Uuid::generate(4);
        }
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
     * @param int|bool $hashLength false for UUID, true for getting it from getDefaultHashLength()
     * @param bool $throwExceptionOnFailureToFindHash Whether to throw exception on failure to find unused hash
     * @return static
     */
    public static function makeWithHash(array $attributes = [], $hashLength = false, $throwExceptionOnFailureToFindHash = true)
    {
        $instance = new static($attributes);
        $instance->addHash($hashLength, true, $throwExceptionOnFailureToFindHash);

        return $instance;
    }

    /**
     * @param array $attributes
     * @param int|bool $hashLength false for UUID, true for getting it from getDefaultHashLength()
     * @return static
     */
    public static function generateWithHash(array $attributes = [], $hashLength = false)
    {
        $instance = static::makeWithHash($attributes, $hashLength, true);
        $instance->save();

        return $instance;
    }

    /**
     * @param int|bool $length false for UUID, true for getting it from getDefaultHashLength()
     * @param int|bool $attempts
     * @param bool $throwExceptionOnFailure
     * @return $this
     */
    public function addHash($length = false, $attempts = true, $throwExceptionOnFailure = true)
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
