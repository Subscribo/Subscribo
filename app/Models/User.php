<?php namespace Subscribo\App\Model;

use Illuminate\Contracts\Auth\Authenticatable;
use Subscribo\RestCommon\Interfaces\ByTokenIdentifiableInterface;
use Subscribo\Auth\Interfaces\CanBeGuestInterface;

/**
 * Model User
 *
 * Model class for being changed and used in the application
 */
class User extends Base\User implements Authenticatable, ByTokenIdentifiableInterface, CanBeGuestInterface
{
    use \Illuminate\Auth\Authenticatable;

    const TYPE_GUEST = 'guest';
    const TYPE_SUPER_ADMIN = 'superadmin';
    const TYPE_ADMINISTRATOR = 'administrator';
    const TYPE_SERVER = 'server';

    public function isGuest()
    {
        return $this->type === self::TYPE_GUEST;
    }

    public static function findGuest()
    {
        $query = self::query();
        $query->where('type', self::TYPE_GUEST);
        $result = $query->first();
        return $result;
    }

}
