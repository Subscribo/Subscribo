<?php namespace Subscribo\ModelCore\Models;

use RuntimeException;

/**
 * Model ActionInterruption
 *
 * Model class for being changed and used in the application
 */
class ActionInterruption extends \Subscribo\ModelCore\Bases\ActionInterruption
{
    const STATUS_WAITING = 'waiting';
    const STATUS_PROCESSED = 'processed';

    public static function make(array $attributes = array())
    {
        /** @var ActionInterruption $instance */
        $instance = new static($attributes);
        $instance->hash = $instance->hash ?: static::assembleUnusedHash();
        $instance->status = $instance->status ?: self::STATUS_WAITING;
        return $instance;
    }

    /**
     * @param $hash
     * @return ActionInterruption|null|static
     */
    public static function findByHash($hash)
    {
        $query = static::query();
        $query->where('hash', $hash);
        $instance = $query->first();
        return $instance;
    }

    public static function assembleUnusedHash($maximalAttemptCount = 10)
    {
        for ($i = 0; $i < $maximalAttemptCount; $i++) {
            $hash = static::assembleRandomString();
            $found = static::findByHash($hash);
            if (empty($found)) {
                return $hash;
            }
        }
        throw new RuntimeException('ActionInterruption::assembleUnusedHash() did not found unused hash within allowed maximalAttemptCount');
    }

    protected static function assembleRandomString($length = 32)
    {
        $result = base64_encode(openssl_random_pseudo_bytes($length));
        $result = strtr($result, ['+' => '', '/' => '', '=' => '']);
        $result = str_pad($result, $length, 'ABCDEFGH');
        $result = substr($result, 0, $length);
        return $result;
    }


    public function getExtraDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setExtraDataAttribute($value)
    {
        $this->attributes['extra_data'] = json_encode($value);
    }

    public function getQuestionaryAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setQuestionaryAttribute($value)
    {
        $this->attributes['questionary'] = json_encode($value);
    }

    public function getAnswerAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setAnswerAttribute($value)
    {
        $this->attributes['answer'] = json_encode($value);
    }

    public function markAsProcessed($answer = null)
    {
        if ( ! is_null($answer)) {
            $this->answer = $answer;
        }
        $this->status = static::STATUS_PROCESSED;
        $this->save();
        return $this;
    }

}
