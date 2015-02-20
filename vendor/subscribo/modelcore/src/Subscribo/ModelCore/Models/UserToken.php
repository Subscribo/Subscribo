<?php namespace Subscribo\ModelCore\Models;

use InvalidArgumentException;
use Subscribo\RestCommon\TokenRing;
use Subscribo\RestCommon\Signature;
use Subscribo\RestCommon\Interfaces\TokenRingProviderInterface;
use Subscribo\ModelCore\Models\User;

/**
 * Model UserToken
 *
 * Model class for being changed and used in the application
 */
class UserToken extends \Subscribo\ModelCore\Bases\UserToken implements TokenRingProviderInterface {

    const TYPE_SUBSCRIBO_BASIC = Signature::TYPE_SUBSCRIBO_BASIC;
    const TYPE_SUBSCRIBO_DIGEST = Signature::TYPE_SUBSCRIBO_DIGEST;
    const TYPE_COMMON_SECRET_ONLY = 'CommonSecretOnly';

    const DEFAULT_TOKEN_TYPE = self::TYPE_SUBSCRIBO_DIGEST;

    const STATUS_ACTIVE = 'active';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->refreshTokenRing();
    }

    /**
     * @return $this
     */
    public function refreshTokenRing()
    {
        $tokenRing = new TokenRing();
        $tokenRing->commonSecret = $this->commonSecret;

        switch ($this->type)
        {
            case self::TYPE_SUBSCRIBO_BASIC:
                $tokenRing->basicToken = $this->token;
                break;
            case self::TYPE_SUBSCRIBO_DIGEST:
                $tokenRing->digestToken = $this->token;
                $tokenRing->digestSecret = $this->secret;
                break;
            case self::TYPE_COMMON_SECRET_ONLY:
        }
        $this->tokenRing = $tokenRing->export();
        return $this;
    }

    /**
     * @return string|null
     */
    public function provideTokenRing()
    {
        return $this->tokenRing;
    }

    /**
     * @return null|User|\Subscribo\RestCommon\Interfaces\ByTokenIdentifiableInterface
     */
    public function provideByTokenIdentifiable()
    {
        return $this->user;
    }

    /**
     * @param $token
     * @param null $tokenType
     * @return UserToken|static|null
     */
    public static function findByTokenAndType($token, $tokenType = null)
    {
        $query = static::query();
        $query->where('token', $token);
        if ($tokenType) {
            $query->where('type', $tokenType);
        }
        $result = $query->first();
        return $result;
    }

    /**
     * @param int|User $user
     * @param bool $tokenType
     * @param null $commonSecret
     * @return UserToken|static
     * @throws InvalidArgumentException
     */
    public static function generateTokenForUser($user, $tokenType = true, $commonSecret = null)
    {
        $userId = ($user instanceof User) ? $user->id : $user;
        if (( ! is_int($userId)) and ( ! ctype_digit($userId))) {
            throw new InvalidArgumentException('user should be either instance of User, int or int string');
        }
        $userId = intval($userId);
        if (true === $tokenType) {
            $tokenType = self::DEFAULT_TOKEN_TYPE;
        }
        $instance = new static();
        $instance->type = $tokenType;
        $instance->userId = $userId;
        $instance->status = self::STATUS_ACTIVE;
        $instance->commonSecret = $commonSecret;

        switch ($tokenType) {
            case self::TYPE_SUBSCRIBO_DIGEST:
                $instance->secret = static::assembleToken();
                $instance->token = static::assembleToken();
                break;
            case self::TYPE_SUBSCRIBO_BASIC:
                $instance->token = static::assembleToken();
                break;
            case self::TYPE_COMMON_SECRET_ONLY:
        }
        $instance->refreshTokenRing();
        $instance->save();
        return $instance;
    }

    protected static function assembleToken($length = 32)
    {
        return Signature::generateRandomString($length);
    }
}
