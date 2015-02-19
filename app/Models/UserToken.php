<?php namespace Subscribo\App\Model;

use Subscribo\RestCommon\TokenRing;
use Subscribo\RestCommon\Signature;
use Subscribo\RestCommon\Interfaces\TokenRingProviderInterface;
use Subscribo\App\Model\User;

/**
 * Model UserToken
 *
 * Model class for being changed and used in the application
 */
class UserToken extends Base\UserToken implements TokenRingProviderInterface {

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

    public function provideTokenRing()
    {
        return $this->tokenRing;
    }

    public function provideByTokenIdentifiable()
    {
        return $this->user;
    }

    /**
     * @param $token
     * @param null $tokenType
     * @return self|null
     */
    public static function findByTokenAndType($token, $tokenType = null)
    {
        $query = self::query();
        $query->where('token', $token);
        if ($tokenType) {
            $query->where('type', $tokenType);
        }
        $result = $query->first();
        return $result;
    }

    public static function generateTokenForUser($user, $tokenType = true, $commonSecret = null)
    {
        $userId = ($user instanceof User) ?  $user = $user->id : $user;
        if (true === $tokenType) {
            $tokenType = self::DEFAULT_TOKEN_TYPE;
        }
        $instance = new self();
        $instance->type = $tokenType;
        $instance->userId = $userId;
        $instance->status = self::STATUS_ACTIVE;
        $instance->commonSecret = $commonSecret;

        switch ($tokenType) {
            case self::TYPE_SUBSCRIBO_DIGEST:
                $instance->secret = self::assembleToken();
                $instance->token = self::assembleToken();
                break;
            case self::TYPE_SUBSCRIBO_BASIC:
                $instance->token = self::assembleToken();
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
