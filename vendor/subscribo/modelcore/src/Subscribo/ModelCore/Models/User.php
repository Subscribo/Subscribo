<?php namespace Subscribo\ModelCore\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Subscribo\RestCommon\Interfaces\ByTokenIdentifiableInterface;
use Subscribo\Auth\Interfaces\CanBeGuestInterface;

/**
 * Model User
 *
 * Model class for being changed and used in the application
 */
class User extends \Subscribo\ModelCore\Bases\User implements Authenticatable, ByTokenIdentifiableInterface, CanBeGuestInterface
{
    use \Illuminate\Auth\Authenticatable;

    const TYPE_GUEST = 'guest';
    const TYPE_SUPER_ADMIN = 'superadmin';
    const TYPE_ADMINISTRATOR = 'administrator';
    const TYPE_SERVER = 'server';

    /**
     * @return bool
     */
    public function isGuest()
    {
        return $this->type === self::TYPE_GUEST;
    }

    /**
     * @return CanBeGuestInterface|User|static|null
     */
    public static function findGuest()
    {
        $query = static::query();
        $query->where('type', self::TYPE_GUEST);
        $result = $query->first();
        return $result;
    }

}
